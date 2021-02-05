<?php


namespace App\Tests\Behat\Context\Setup;


use App\Component\Messenger\HandleTrait;
use App\Entity\User;
use App\Message\ShortUrl\CreateShortUrlMessage;
use App\Tests\Behat\Service\SharedStorage;
use Behat\Behat\Context\Context;
use Symfony\Component\Messenger\MessageBusInterface;

class ShortUrlContext implements Context
{
    use HandleTrait;

    /**
     * @var SharedStorage
     */
    private $sharedStorage;

    public function __construct(
        MessageBusInterface $messageBus,
        SharedStorage $sharedStorage
    )
    {
        $this->messageBus = $messageBus;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given /^I have shorted the long url "([^"]*)"$/
     */
    public function iHaveTheLongUrl(string $url)
    {
        /** @var User $user */
        $user = $this->sharedStorage->get('user');

        $shortUrl = $this->handle(
            new CreateShortUrlMessage(
                $user,
                $url,
                null
            )
        );

        $this->sharedStorage->set('short_url', $shortUrl);
    }
}
