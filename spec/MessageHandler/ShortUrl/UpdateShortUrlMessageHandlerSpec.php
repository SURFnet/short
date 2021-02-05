<?php

namespace spec\App\MessageHandler\ShortUrl;

use App\Entity\ShortUrl;
use App\Exception\ShortUrlNotFoundException;
use App\Message\ShortUrl\UpdateShortUrlMessage;
use App\Repository\ShortUrlRepository;
use PhpSpec\ObjectBehavior;

class UpdateShortUrlMessageHandlerSpec extends ObjectBehavior
{
    private const SHORT_URL_ID = '16aa92e9-f794-44b7-9854-f16eb4ac9ca2';
    private const LONG_URL = 'https://long.url/example';

    public function let(
        ShortUrlRepository $shortUrlRepository
    ) {
        $this->beConstructedWith($shortUrlRepository);
    }

    public function it_updates_a_short_url(
        ShortUrlRepository $shortUrlRepository,
        ShortUrl $shortUrl
    ): void {
        $shortUrlRepository->find(self::SHORT_URL_ID)->willReturn($shortUrl);
        $shortUrl->getDeleted()->willReturn(false);

        $shortUrl->setLongUrl(self::LONG_URL)->shouldBeCalled();
        $shortUrl->setUpdated()->shouldBeCalled();

        $message = new UpdateShortUrlMessage(self::SHORT_URL_ID, self::LONG_URL);
        $this($message);
    }

    public function it_cannot_update_a_deleted_short_url(
        ShortUrlRepository $shortUrlRepository,
        ShortUrl $shortUrl
    ) {
        $shortUrlRepository->find(self::SHORT_URL_ID)->willReturn($shortUrl);
        $shortUrl->getDeleted()->willReturn(true);

        $message = new UpdateShortUrlMessage(self::SHORT_URL_ID, self::LONG_URL);
        $this->shouldThrow(ShortUrlNotFoundException::class)
            ->during('__invoke', [$message]);
    }
}
