<?php

namespace App\Security\OAuth2\Client\Provider;


use App\Security\OAuth2\Checker\IdpHintChecker;
use Jose\Component\Checker\AudienceChecker;
use Jose\Component\Checker\ClaimCheckerManager;
use Jose\Component\Checker\ExpirationTimeChecker;
use Jose\Component\Checker\IssuedAtChecker;
use Jose\Component\Checker\IssuerChecker;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class InAcademia extends AbstractProvider
{
    /**
     * @var string
     */
    protected $discoverEndpoint;

    public function __construct(array $options = [], array $collaborators = [])
    {
        if (empty($options['discoverEndpoint'])) {
            $message = 'The "discoverEndpoint" option is not set.';
            throw new \InvalidArgumentException($message);
        }

        parent::__construct($options, $collaborators);
    }

    public function getIssuer()
    {
        return $this->getOpenIdProviderAttribute('issuer');
    }

    public function getBaseAuthorizationUrl()
    {
        return $this->getOpenIdProviderAttribute('authorization_endpoint');
    }

    public function getBaseJwksUri()
    {
        return $this->getOpenIdProviderAttribute('jwks_uri');
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        throw new \RuntimeException('Access Token is not supported by InAcademia OP');
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        throw new \RuntimeException('Resource Owner is not supported by InAcademia OP');
    }

    protected function getDefaultScopes()
    {
        return ['openid', 'member', 'persistent'];
    }

    protected function getScopeSeparator()
    {
        return " ";
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['error'])) {
            $code = 0;
            $error = $data['error'];
            if (\is_array($error)) {
                $code = $error['code'];
                $error = $error['message'];
            }

            throw new IdentityProviderException($error, $code, $data);
        }
    }

    public function getResourceOwner(AccessToken $token)
    {
        $idToken = $token->getToken();

        return new InAcademiaResourceOwner($this->getClaims($idToken));
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        throw new \RuntimeException('Not supported');
    }

    public function getAccessToken($grant, array $options = [])
    {
        $idToken = $options['code'];
        $idpHint = $options['idp_hint'];

        $this->checkClaims($idToken, $idpHint);
        $this->checkJsonWebSignature($idToken);

        return new AccessToken(['access_token' => $idToken]);
    }

    private function getOpenIdProviderAttribute(string $attribute)
    {
        $response = $this->getHttpClient()
            ->request('GET', $this->discoverEndpoint);
        $attributes = json_decode($response->getBody(), true);

        if (!isset($attributes[$attribute])) {
            throw new \RuntimeException(sprintf('Attribute "%s" not found', $attribute));
        }

        return $attributes[$attribute];
    }

    private function checkClaims(string $idToken, ?string $idpHint): void
    {
        $checker = new ClaimCheckerManager([
            new IssuerChecker([$this->getIssuer()]),
            new AudienceChecker($this->clientId),
            new ExpirationTimeChecker(),
            new IssuedAtChecker(),
            new IdpHintChecker($idpHint)
        ]);

        $checker->check($this->getClaims($idToken));
    }

    private function checkJsonWebSignature(string $idToken)
    {
        $jsonWebSignature = $this->getJsonWebSignature($idToken);
        $jsonWebKeySet = $this->getJsonWebKeySet();
        $jwsVerifier = $this->getJWSVerifier();

        if (!$jwsVerifier->verifyWithKeySet($jsonWebSignature, $jsonWebKeySet, 0)) {
            throw new \RuntimeException('Invalid JSON Web Signature');
        }
    }

    private function getClaims(string $idToken)
    {
        return \json_decode($this->getJsonWebSignature($idToken)->getPayload(), true);
    }

    private function getJsonWebSignature(string $idToken)
    {
        $serializerManager = new JWSSerializerManager([
            new CompactSerializer(),
        ]);

        return $serializerManager->unserialize($idToken);
    }

    private function getJsonWebKeySet()
    {
        $response = $this->getHttpClient()
            ->request('GET', $this->getBaseJwksUri());
        $rawJsonWebKeySet = $response->getBody();

        return JWKFactory::createFromJsonObject($rawJsonWebKeySet);
    }

    private function getJWSVerifier()
    {
        return new JWSVerifier(
            new AlgorithmManager([
                new RS256(),
            ])
        );
    }
}
