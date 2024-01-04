<?php


namespace App\Form\Model;


use App\Entity\ShortUrl;
use App\Validator\LongUrl;
use App\Validator\NotBannedDomain;
use Symfony\Component\Validator\Constraints as Assert;

final class ShortUrlModel
{
    /**
     * @Assert\NotBlank()
     * @LongUrl()
     * @NotBannedDomain()
     * @var string
     */
    public $longUrl;

    /**
     * @var string
     */
    public $label;

    public static function fromShortUrl(ShortUrl $shortUrl): self
    {
        $instance = new self();
        $instance->longUrl = $shortUrl->getLongUrl();
        $instance->label = $shortUrl->getLabel();

        return $instance;
    }
}
