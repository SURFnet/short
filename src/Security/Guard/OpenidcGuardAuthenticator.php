<?php

namespace App\Security\Guard;


use App\Entity\User;
use App\Message\User\ProvideUserMessage;
use App\Repository\InstitutionRepository;
use App\Security\OAuth2\Client\Provider\InAcademiaResourceOwner;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
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
    /**
     * @var InstitutionRepository
     */
    private $institutionRepository;
    /**
     * @var LoginLinkHandlerInterface
     */
    private $institutionalLoginLinkHandler;

    public function __construct(
        ClientRegistry $clientRegistry,
        RouterInterface $router,
        MessageBusInterface $messageBus,
        InstitutionRepository $institutionRepository,
        LoginLinkHandlerInterface $institutionalLoginLinkHandler
    )
    {
        $this->clientRegistry = $clientRegistry;
        $this->router = $router;
        $this->messageBus = $messageBus;
        $this->institutionRepository = $institutionRepository;
        $this->institutionalLoginLinkHandler = $institutionalLoginLinkHandler;
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
        $idpHint = $request->getSession()->get('_return_url');

        return $this->fetchAccessToken($this->getClient(), ['idp_hint' => $idpHint]);
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
        $targetPath = $this->getInstitutionLoginPath($request->getSession(), $token);

        if (!$targetPath) {
            $targetPath = $this->getTargetPath($request->getSession(), $providerKey);
        }

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
            new ProvideUserMessage(
                $userResource->getId(),
                $userResource->getIdpHint()
            )
        );
    }

    private function getInstitutionLoginPath(SessionInterface $session, TokenInterface $token): ?string
    {
        if (!$institutionHash = $session->get('_return_url')) {
            return null;
        }

        if (!$institution = $this->institutionRepository->findOneBy(['hash' => $institutionHash])) {
            return null;
        }

        if (!$user = $token->getUser()) {
            return null;
        }

        $context = $this->router->getContext();
        $context->setHost($institution->getDomain());

        return $this->institutionalLoginLinkHandler
            ->createLoginLink($user)
            ->getUrl();
    }
}
