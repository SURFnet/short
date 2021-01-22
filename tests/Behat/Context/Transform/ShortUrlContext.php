<?php


namespace App\Tests\Behat\Context\Transform;


use App\Entity\ShortUrl;
use App\Repository\ShortUrlRepository;
use Behat\Behat\Context\Context;
use Webmozart\Assert\Assert;

class ShortUrlContext implements Context
{
    /**
     * @var ShortUrlRepository
     */
    private $shortUrlRepository;

    public function __construct(ShortUrlRepository $shortUrlRepository)
    {
        $this->shortUrlRepository = $shortUrlRepository;
    }

    /**
     * @Transform :code
     */
    public function getShortUrlByShortUrl(string $code): ShortUrl
    {
        $shortUrls = $this->shortUrlRepository->findBy([
            'shortUrl' => $code,
        ]);

        Assert::eq(
            count($shortUrls),
            1,
            sprintf('%d short urls has been found with short url', count($shortUrls), $code)
        );

        return $shortUrls[0];
    }

    /**
     * @Transform :shortUrl
     */
    public function getShortUrlByLongUrl(string $longUrl): ShortUrl
    {
        $shortUrls = $this->shortUrlRepository->findBy([
            'longUrl' => $longUrl,
        ]);

        Assert::eq(
            count($shortUrls),
            1,
            sprintf('%d short urls has been found with long url', count($shortUrls), $longUrl)
        );

        return $shortUrls[0];
    }
}
