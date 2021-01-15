<?php

namespace App\Tests\Behat\Service;

use Behat\Mink\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

final class SecurityService
{
    private const SESSION_TOKEN = '_security_main';

    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var Session
     */
    private $minkSession;

    public function __construct(SessionInterface $session, Session $minkSession)
    {
        $this->session = $session;
        $this->minkSession = $minkSession;
    }

    public function logIn(UserInterface $user): void
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
        $this->setToken($token);
    }

    private function setToken(TokenInterface $token): void
    {
        $this->session->set(self::SESSION_TOKEN, serialize($token));
        $this->session->save();

        $this->minkSession->setCookie($this->session->getName(), $this->session->getId());
    }
}
