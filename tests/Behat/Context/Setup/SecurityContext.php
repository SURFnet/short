<?php

namespace App\Tests\Behat\Context\Setup;

use App\Component\Messenger\HandleTrait;
use App\Message\User\AddUserMessage;
use App\Tests\Behat\Service\SecurityService;
use Behat\Behat\Context\Context;
use Symfony\Component\Messenger\MessageBusInterface;

final class SecurityContext implements Context
{
    use HandleTrait;

    /**
     * @var SecurityService
     */
    private $securityService;

    public function __construct(MessageBusInterface $messageBus, SecurityService $securityService)
    {
        $this->messageBus = $messageBus;
        $this->securityService = $securityService;
    }

    /**
     * @Given /^I am logged as an user$/
     */
    public function iAmLoggedAsAnUser()
    {
        $user = $this->handle(new AddUserMessage('student', ['ROLE_USER']));
        $this->securityService->logIn($user);
    }
}
