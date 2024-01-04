<?php

namespace App\MessageHandler\ShortUrl;

use App\Entity\ShortUrl;
use App\Exception\ShortUrlNotFoundException;
use App\Message\ShortUrl\UpdateShortUrlMessage;
use App\Repository\ShortUrlRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UpdateShortUrlMessageHandler implements MessageHandlerInterface
{
    /**
     * @var ShortUrlRepository
     */
    private $shortUrlRepository;

    public function __construct(ShortUrlRepository $shortUrlRepository)
    {
        $this->shortUrlRepository = $shortUrlRepository;
    }

    public function __invoke(UpdateShortUrlMessage $message)
    {
        $id = $message->getId();
        $longUrl = $message->getLongUrl();
        $label = $message->getLabel();

        $shortUrl = $this->shortUrlRepository->find($id);

        if (!$shortUrl instanceof ShortUrl || $shortUrl->getDeleted()) {
            throw ShortUrlNotFoundException::becauseIsDeleted();
        }

        $shortUrl->setLongUrl($longUrl);
        $shortUrl->setLabel($label);
        $shortUrl->setUpdated();
    }
}
