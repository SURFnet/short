<?php


namespace App\Form\Model;


use App\Entity\ShortUrl;
use App\Validator\NotBannedDomain;
use Symfony\Component\Validator\Constraints as Assert;

final class ShortUrlModel
{
    /**
     * @Assert\Url(message="shorturl.invalid_url")
     * @Assert\NotBlank()
     * @NotBannedDomain()
     * @var string
     */
    public $longUrl;

    public static function fromShortUrl(ShortUrl $shortUrl): self
    {
        $instance = new self();
        $instance->longUrl = $shortUrl->getLongUrl();

        return $instance;
    }
}
