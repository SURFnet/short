<?php


namespace App\Security\Guard;


use App\Message\User\ProvideUserMessage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class ApacheModAuthOpenidcGuardAuthenticator extends AbstractGuardAuthenticator
{
    use TargetPathTrait;
    use HandleTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router, MessageBusInterface $messageBus)
    {
        $this->router = $router;
        $this->messageBus = $messageBus;
    }

    public function supports(Request $request)
    {
        return 'connect_openidc_check' === $request->attributes->get('_route');
    }

    public function getCredentials(Request $request)
    {
        return $request->server->get('REDIRECT_REMOTE_USER');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (null === $credentials) {
            return null;
        }

        return $this->handle(
            new ProvideUserMessage($credentials)
        );
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
            $targetPath = $this->router->generate('app_manage_index');
        }

        return new RedirectResponse($targetPath);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            $this->router->generate('connect_openidc_start'),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
