<?php

namespace App\MessageHandler\ShortUrl;

use App\Entity\ShortUrl;
use App\Entity\User;
use App\Exception\ShortCodeNotAvailableException;
use App\Message\ShortUrl\CreateShortUrlMessage;
use App\Repository\InstitutionRepository;
use App\Repository\ShortUrlRepository;
use App\Repository\UserRepository;
use App\Services\GenerateUniqueShortUrl;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CreateShortUrlMessageHandler implements MessageHandlerInterface
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
    /**
     * @var InstitutionRepository
     */
    private $institutionRepository;

    public function __construct(
        UserRepository $userRepository,
        ShortUrlRepository $shortUrlRepository,
        GenerateUniqueShortUrl $generateUniqueShortUrl,
        InstitutionRepository $institutionRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->shortUrlRepository = $shortUrlRepository;
        $this->generateUniqueShortUrl = $generateUniqueShortUrl;
        $this->institutionRepository = $institutionRepository;
    }

    public function __invoke(CreateShortUrlMessage $message)
    {
        /** @var User $owner */
        $owner = $this->userRepository->find($message->getOwnerId());
        $institution = $this->institutionRepository->findOneBy(['domain' => $message->getDomain()]);

        $uniqueCode = $this->getUniqueCode($message);

        $shortUrl = new ShortUrl();
        $shortUrl->setOwner($owner);
        $shortUrl->setLongUrl($message->getLongUrl());
        $shortUrl->setShortUrl($uniqueCode);
        $shortUrl->setInstitution($institution);

        $this->shortUrlRepository->save($shortUrl);

        return $shortUrl;
    }

    /**
     * @param CreateShortUrlMessage $message
     * @return string
     */
    private function getUniqueCode(CreateShortUrlMessage $message): string
    {
        $code = $message->getShortUrl();

        if (!$code) {
            return $this->generateUniqueShortUrl->getUniqueShortUrlCode();
        }

        if (!$this->generateUniqueShortUrl->checkShortUrlCodeIsAvailable($code)) {
            throw ShortCodeNotAvailableException::becauseAlreadyUsed();
        }

        return $code;
    }
}
