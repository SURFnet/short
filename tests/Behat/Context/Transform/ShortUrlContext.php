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
            sprintf('%d short urls has been found with long url %s', count($shortUrls), $longUrl)
        );

        return $shortUrls[0];
    }
}
