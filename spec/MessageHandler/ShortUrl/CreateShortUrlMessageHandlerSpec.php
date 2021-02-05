<?php

namespace spec\App\MessageHandler\ShortUrl;

use App\Entity\ShortUrl;
use App\Entity\User;
use App\Exception\ShortCodeNotAvailableException;
use App\Message\ShortUrl\CreateShortUrlMessage;
use App\Repository\ShortUrlRepository;
use App\Repository\UserRepository;
use App\Services\GenerateUniqueShortUrl;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CreateShortUrlMessageHandlerSpec extends ObjectBehavior
{
    private const OWNER_ID = '16aa92e9-f794-44b7-9854-f16eb4ac9ca2';
    private const LONG_URL = 'https://long.url/example';
    private const SHORT_URL = 'code';

    public function let(
        UserRepository $userRepository,
        ShortUrlRepository $shortUrlRepository,
        GenerateUniqueShortUrl $generateUniqueShortUrl,
        User $user
    ) {
        $this->beConstructedWith($userRepository, $shortUrlRepository, $generateUniqueShortUrl);

        $userRepository->find(self::OWNER_ID)->willReturn($user);
    }

    public function it_creates_a_random_short_url(
        GenerateUniqueShortUrl $generateUniqueShortUrl,
        ShortUrlRepository $shortUrlRepository
    ): void {
        $generateUniqueShortUrl->getUniqueShortUrlCode()->shouldBeCalled()->willReturn(self::SHORT_URL);
        $generateUniqueShortUrl->checkShortUrlCodeIsAvailable(Argument::any())->shouldNotBeCalled();

        $shortUrlRepository->save(Argument::that(
            function (ShortUrl $shortUrl) {
                return self::SHORT_URL === $shortUrl->getShortUrl();
            }
        ))->shouldBeCalled();

        $this(new CreateShortUrlMessage(self::OWNER_ID, self::LONG_URL, null));
    }

    public function it_creates_a_custom_short_url(
        GenerateUniqueShortUrl $generateUniqueShortUrl,
        ShortUrlRepository $shortUrlRepository
    ): void {
        $generateUniqueShortUrl->getUniqueShortUrlCode()->shouldNotBeCalled();
        $generateUniqueShortUrl->checkShortUrlCodeIsAvailable(self::SHORT_URL)->willReturn(true);

        $shortUrlRepository->save(Argument::that(
            function (ShortUrl $shortUrl) {
                return self::SHORT_URL === $shortUrl->getShortUrl();
            }
        ))->shouldBeCalled();

        $this(new CreateShortUrlMessage(self::OWNER_ID, self::LONG_URL, self::SHORT_URL));
    }

    public function it_checks_is_short_url_is_available(
        GenerateUniqueShortUrl $generateUniqueShortUrl,
        ShortUrlRepository $shortUrlRepository
    ): void {
        $generateUniqueShortUrl->getUniqueShortUrlCode()->shouldNotBeCalled();
        $generateUniqueShortUrl->checkShortUrlCodeIsAvailable(self::SHORT_URL)->willReturn(false);

        $this->shouldThrow(ShortCodeNotAvailableException::class)->during('__invoke', [
            new CreateShortUrlMessage(self::OWNER_ID, self::LONG_URL, self::SHORT_URL),
        ]);
    }
}
