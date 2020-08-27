<?php


namespace App\Security\Guard;


use App\Security\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class ApacheModAuthMellonGuardAuthenticator extends AbstractGuardAuthenticator
{
    use TargetPathTrait;

    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var string
     */
    private $modAuthMellonRoleAttribute;
    /**
     * @var string
     */
    private $modAuthMellonRoleValue;

    public function __construct(
        RouterInterface $router,
        string $modAuthMellonRoleAttribute,
        string $modAuthMellonRoleValue
    ) {
        $this->router = $router;
        $this->modAuthMellonRoleAttribute = $modAuthMellonRoleAttribute;
        $this->modAuthMellonRoleValue = $modAuthMellonRoleValue;
    }

    public function supports(Request $request)
    {
        return 'connect_mellon_check' === $request->attributes->get('_route');
    }

    public function getCredentials(Request $request)
    {
        return [
            'username' => $request->server->get('REDIRECT_REMOTE_USER'),
            'admin' => $this->checkIsAdmin($request),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (null === $credentials['username']) {
            return null;
        }

        return new User($credentials['username'], $credentials['admin']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);

        if (!$targetPath) {
            $targetPath = $this->router->generate('app_info_index');
        }

        $request->getSession()->set('mod_auth_mellon', true);

        return new RedirectResponse($targetPath);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            $this->router->generate('connect_mellon_start'),
            Response::HTTP_TEMPORARY_REDIRECT
        );

    }

    public function supportsRememberMe()
    {
        return false;
    }

    private function checkIsAdmin(Request $request): bool
    {
        $idx = 0;
        while(1) {
            $attribute = sprintf('REDIRECT_MELLON_%s_%d', $this->modAuthMellonRoleAttribute, $idx);

            if (!$request->server->has($attribute)) {
                return false;
            }

            if ($request->server->get($attribute) === $this->modAuthMellonRoleValue) {
                return true;
            }

            $idx++;
        }
    }
}
