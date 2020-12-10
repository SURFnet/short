<?php

namespace App\MessageHandler\ShortUrl;

use App\Entity\ShortUrl;
use App\Exception\ShortCodeNotAvailableException;
use App\Message\ShortUrl\CreateCustomShortUrlMessage;
use App\Repository\ShortUrlRepository;
use App\Repository\UserRepository;
use App\Services\GenerateUniqueShortUrl;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CreateCustomShortUrlMessageHandler implements MessageHandlerInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var ShortUrlRepository
     */
    private $shortUrlRepository;
    /**
     * @var GenerateUniqueShortUrl
     */
    private $generateUniqueShortUrl;

    public function __construct(UserRepository $userRepository, ShortUrlRepository $shortUrlRepository, GenerateUniqueShortUrl $generateUniqueShortUrl)
    {
        $this->userRepository = $userRepository;
        $this->shortUrlRepository = $shortUrlRepository;
        $this->generateUniqueShortUrl = $generateUniqueShortUrl;
    }

    public function __invoke(CreateCustomShortUrlMessage $message)
    {
        if (!$this->generateUniqueShortUrl->checkShortUrlCodeIsAvailable($message->getShorUrl())) {
            throw ShortCodeNotAvailableException::with($message->getShorUrl());
        }

        $owner = $this->userRepository->find($message->getOwnerId());

        $shortUrl = new ShortUrl();
        $shortUrl->setOwner($owner);
        $shortUrl->setLongUrl($message->getLongUrl());
        $shortUrl->setShortUrl($message->getShorUrl());

        $this->shortUrlRepository->save($shortUrl);

        return $shortUrl;
    }
}
