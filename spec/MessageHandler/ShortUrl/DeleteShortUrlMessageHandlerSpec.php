<?php

namespace spec\App\MessageHandler\ShortUrl;

use App\Entity\ShortUrl;
use App\Exception\ShortUrlNotFoundException;
use App\Message\ShortUrl\DeleteShortUrlMessage;
use App\Repository\ShortUrlRepository;
use PhpSpec\ObjectBehavior;

class DeleteShortUrlMessageHandlerSpec extends ObjectBehavior
{
    private const SHORT_URL_ID = '16aa92e9-f794-44b7-9854-f16eb4ac9ca2';

    public function let(
        ShortUrlRepository $shortUrlRepository
    ) {
        $this->beConstructedWith($shortUrlRepository);
    }

    public function it_deletes_a_short_url(
        ShortUrlRepository $shortUrlRepository,
        ShortUrl $shortUrl
    ): void {
        $shortUrlRepository->find(self::SHORT_URL_ID)->willReturn($shortUrl);
        $shortUrl->getDeleted()->willReturn(false);

        $shortUrl->setDeleted(true)->shouldBeCalled();
        $shortUrl->setUpdated()->shouldBeCalled();

        $message = new DeleteShortUrlMessage(self::SHORT_URL_ID);
        $this($message);
    }

    public function it_cannot_delete_a_short_url_twice(
        ShortUrlRepository $shortUrlRepository,
        ShortUrl $shortUrl
    ) {
        $shortUrlRepository->find(self::SHORT_URL_ID)->willReturn($shortUrl);
        $shortUrl->getDeleted()->willReturn(true);

        $message = new DeleteShortUrlMessage(self::SHORT_URL_ID);
        $this->shouldThrow(ShortUrlNotFoundException::class)
            ->during('__invoke', [$message]);
    }
}
