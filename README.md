# SURFshort

Code running SURFshort.

It requires:
- Apache with PHP >= 7.2
- Apache module Mod_auth_mellon
- A MySQL database (can be easily generalised but not necessary right now)

## Installation

```bash
apt install git composer php-fpm apache2 libapache2-mod-auth-mellon \
    php-xml php-intl  php-mysql mariadb-server
a2enmod proxy_fcgi rewrite header ssl
composer install
```

Create a database and user and grant privileges:

```sql
CREATE USER 'short'@'localhost' IDENTIFIED BY 'longpassword';
GRANT ALL PRIVILEGES ON short.* TO 'short'@'localhost';
```

In .env.local:
```
DATABASE_URL=mysql://short:longpassword@127.0.0.1:3306/short?serverVersion=5.7
```

## Configuration

In config/services.yaml you can find the basic parameters:

```yaml
parameters:
    app.name: "SURFshort"
    app.urldomain: "example.org"
    app.payoff: "Dé URL-shortener voor onderwijs en onderzoek met respect voor privacy."
    app.security.logouturl: "/mellon/logout?ReturnTo=/"
    app.shortcode.length: 5
    app.shortcode.maxtries: 50
    app.shortcode.chars: 'abcdefghjkmnpqrtuvwxy346789'
    # note: should match routes.yaml regexp
    app.shortcode.forbiddenchars: '/[^a-z0-9-]/'
    app.health.minimumurls: 10
```

## Webserver configuration

### With SAML authentication

SAML authentication uses the Apache [mellon authentication module](https://github.com/latchset/mod_auth_mellon).
Install and configure mod_mellon according to its instructions.

```apacheconfig
Protocols h2 h2c http/1.1

<VirtualHost _default_:80>
    Servername example.org
    ServerAdmin root@example.org

    Redirect permanent / https://example.org/
</VirtualHost>

<VirtualHost _default_:443>
    Servername example.org
    ServerAdmin root@example.org

    DocumentRoot /srv/www/short/public

    <Directory />
            Options FollowSymLinks
            AllowOverride None
    </Directory>

    <Directory /srv/www/short/public>
        AllowOverride None
        Require all granted

        AddHandler "proxy:unix:/run/php/php7.3-fpm.sock|fcgi://localhost" .php

        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^(.*)$ index.php [QSA,L]
    </Directory>

    <Location />
        MellonSPentityId "https://example.org"
        MellonSPCertFile /etc/apache2/mellon/saml.crt
        MellonSPPrivateKeyFile /etc/apache2/mellon/saml.key

        MellonIdPMetadataFile /etc/apache2/mellon/idp-metadata.xml
        MellonIdPPublicKeyFile /etc/apache2/mellon/idp-cert.pem

        MellonDefaultLoginPath "/manage/"

        MellonSecureCookie On
        MellonCookieSameSite None

        BrowserMatch "\(iP.+; CPU .*OS 12[_\d]*.*\) AppleWebKit\/" MELLON_DISABLE_SAMESITE=1
        BrowserMatch "\(Macintosh;.*Mac OS X 10_14[_\d]*.*\) AppleWebKit\/" MELLON_DISABLE_SAMESITE=1
        BrowserMatch "^Mozilla\/[\.\d]+ \(Macintosh;.*Mac OS X 10_14[_\d]*.*\) .* AppleWebKit\/[\.\d]+ \(KHTML, like Gecko\)$" MELLON_DISABLE_SAMESITE=1
        BrowserMatch "UCBrowser\/(8|9|10|11)\.(\d+)\.(\d+)[\.\d]* " MELLON_DISABLE_SAMESITE=1
        BrowserMatch "UCBrowser\/12\.13\.[0-1][\.\d]* " MELLON_DISABLE_SAMESITE=1
        BrowserMatch "UCBrowser\/12\.1[0-2]\.(\d+)[\.\d]* " MELLON_DISABLE_SAMESITE=1
        BrowserMatch "UCBrowser\/12\.\d\.(\d+)[\.\d]* " MELLON_DISABLE_SAMESITE=1
        BrowserMatch "Chrom[^ \/]+\/6[0-6][\.\d]* " MELLON_DISABLE_SAMESITE=1
        BrowserMatch "Chrom[^ \/]+\/5[1-9][\.\d]* " MELLON_DISABLE_SAMESITE=1
        BrowserMatch "Outlook-iOS" MELLON_DISABLE_SAMESITE=1
    </Location>

    <Location /manage>
        Header always set X-Frame-Options "DENY"
        Header always set Referrer-Policy "origin"
    </Location>

    <Location /connect/mellon/check>
        AuthType "Mellon"
        Require valid-user
        MellonEnable "auth"
    </Location>

    Header always set Strict-Transport-Security "max-age=31556952"
    #	Header always set Content-Security-Policy: "default-src 'none'; font-src 'self'; script-src 'self'; connect-src 'self'; img-src 'self'; style-src 'self';"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Xss-Protection "1; mode=block"

    SSLEngine on
    SSLCertificateFile         /etc/letsencrypt/live/example.org/cert.pem
    SSLCertificateKeyFile      /etc/letsencrypt/live/example.org/privkey.pem
    SSLCertificateChainFile    /etc/letsencrypt/live/example.org/chain.pem
</VirtualHost>
```

### With OpenID Connect Authentication

OpenID Connect authentication uses the Apache [openidc authentication module](https://github.com/zmartzone/mod_auth_openidc).
Install and configure mod_openidc according to its instructions. For example:

```apacheconfig
<IfModule auth_openidc_module>
    OIDCProviderMetadataURL <issuer>/.well-known/openid-configuration
    OIDCClientID <client_id>
    OIDCClientSecret <client_secret>
    # Configure your site hostname
    OIDCRedirectURI https://<hostname>/connect/oidc/check
    OIDCResponseType id_token
    OIDCScope "openid member persistent"
    OIDCCryptoPassphrase <password>
    #OIDCAuthRequestParams <request_params>

    # Don't change this
    <LocationMatch /connect/oidc/(check|return)>
        AuthType openid-connect
        Require valid-user
    </LocationMatch>
</IfModule>
```

## Configure parameters

Some parameters can be configured with environment variables.

### Authentication method

If you want to enable SAML authentication method set these parameters (enabled by default):

    APP_MOD_SECURITY=mellon
    APP_MOD_LOGOUT="/mellon/logout?ReturnTo=/"

If you want to enable OpenID Connect authentication method set these parameters:

    APP_MOD_SECURITY=openidc
    APP_MOD_LOGOUT="/connect/oidc/return?logout=/"

### Site personalization

* APP_NAME. Configure the name of the site.
* APP_FQDN. Configure the FQDN used to create the shorted urls.
* APP_IDP_NAME. Configure the name of your identity provider.

## Administrator users

### With SAML Authentication

To assign an administrator role to certain users, you need to set up a SAML attribute
where you look for a certain value. Only users that contain that value will be
assigned as administrators.

Assign the environment variables `APP_MOD_AUTH_MELLON_ROLE_ATTRIBUTE` and
`APP_MOD_AUTH_MELLON_ROLE_VALUE` you will find in the .env file. For example:

    APP_MOD_AUTH_MELLON_ROLE_ATTRIBUTE=eduPersonEntitlement
    APP_MOD_AUTH_MELLON_ROLE_VALUE=urn:mace:domain:service:admin

### With OpenID Connect Authentication

It is not yet implemented.

## Health endpoint

There's an endpoint at `/health` which will return a 200 HTTP status code
if the database can be reached and has at least 10 URLs in it, you can
use this for basic monitoring.

## License

The code is under the Apache 2.0 license.

The shipped font Nunito is under the SIL Open Fonts License 1.1.

## Security issues

We'd be extremely grateful if you could report any security issues via
kort@surf.nl.

You are also welcome to use our Responsible Disclosure process.
https://www.surf.nl/responsible-disclosure-surf

## Contact

kort@surf.nl
