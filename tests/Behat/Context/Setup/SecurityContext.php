<?php

namespace App\Tests\Behat\Context\Setup;

use App\Component\Messenger\HandleTrait;
use App\Message\User\AddUserMessage;
use App\Tests\Behat\Service\SecurityService;
use App\Tests\Behat\Service\SharedStorage;
use Behat\Behat\Context\Context;
use Symfony\Component\Messenger\MessageBusInterface;

final class SecurityContext implements Context
{
    use HandleTrait;

    /**
     * @var SecurityService
     */
    private $securityService;
    /**
     * @var SharedStorage
     */
    private $sharedStorage;

    public function __construct(
        MessageBusInterface $messageBus,
        SecurityService $securityService,
        SharedStorage $sharedStorage
    ) {
        $this->messageBus = $messageBus;
        $this->securityService = $securityService;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given /^I am logged as an user$/
     */
    public function iAmLoggedAsAnUser()
    {
        $user = $this->handle(new AddUserMessage('student', ['ROLE_USER']));
        $this->securityService->logIn($user);

        $this->sharedStorage->set('user', $user);
    }

    /**
     * @Given /^I am logged as an administrator$/
     */
    public function iAmLoggedAsAnAdministrator()
    {
        $user = $this->handle(new AddUserMessage('staff', ['ROLE_ADMIN']));
        $this->securityService->logIn($user);

        $this->sharedStorage->set('user', $user);
    }
}
