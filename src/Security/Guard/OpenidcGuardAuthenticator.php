<?php

namespace App\Security\Guard;


use App\Message\User\ProvideUserMessage;
use App\Security\OAuth2\Client\Provider\InAcademiaResourceOwner;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class OpenidcGuardAuthenticator extends SocialAuthenticator
{
    use TargetPathTrait;
    use HandleTrait;

    /**
     * @var ClientRegistry
     */
    private $clientRegistry;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    public function __construct(
        ClientRegistry $clientRegistry,
        RouterInterface $router,
        MessageBusInterface $messageBus
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->router = $router;
        $this->messageBus = $messageBus;
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            $this->router->generate('connect_openidc_start'),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        return 'connect_openidc_check' === $request->attributes->get('_route');
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getClient());
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);

        if (!$targetPath) {
            $targetPath = $this->router->generate('app_manage_index');
        }

        return new RedirectResponse($targetPath);
    }


    private function getClient(): OAuth2ClientInterface
    {
        return $this
            ->clientRegistry
            ->getClient('inacademia');
    }


    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (null === $credentials) {
            return null;
        }

        $userResource = $this
            ->getClient()
            ->fetchUserFromToken($credentials);

        if (!$userResource instanceof InAcademiaResourceOwner) {
            throw new \RuntimeException('Invalidad InAcademiaResource');
        }

        return $this->handle(
            new ProvideUserMessage($userResource->getId())
        );
    }
}
