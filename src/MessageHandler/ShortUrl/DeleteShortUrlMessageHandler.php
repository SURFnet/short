<?php

namespace App\MessageHandler\ShortUrl;

use App\Entity\ShortUrl;
use App\Exception\ShortUrlNotFoundException;
use App\Message\ShortUrl\DeleteShortUrlMessage;
use App\Repository\ShortUrlRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class DeleteShortUrlMessageHandler implements MessageHandlerInterface
{
    /**
     * @var ShortUrlRepository
     */
    private $shortUrlRepository;

    public function __construct(ShortUrlRepository $shortUrlRepository)
    {
        $this->shortUrlRepository = $shortUrlRepository;
    }

    public function __invoke(DeleteShortUrlMessage $message)
    {
        $id = $message->getId();

        $shortUrl = $this->shortUrlRepository->find($id);

        if (!$shortUrl instanceof ShortUrl || $shortUrl->getDeleted()) {
            throw ShortUrlNotFoundException::becauseIsDeleted();
        }

        $shortUrl->setDeleted(true);
        $shortUrl->setUpdated();
    }
}
