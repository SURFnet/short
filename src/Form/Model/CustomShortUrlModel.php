<?php


namespace App\Form\Model;


use App\Validator\NotBannedDomain;
use App\Validator\NotForbiddenChars;
use Symfony\Component\Validator\Constraints as Assert;

final class CustomShortUrlModel
{
    /**
     * @var string|null
     *
     * @NotForbiddenChars()
     */
    public $shortUrl;

    /**
     * @var string
     *
     * @Assert\Url(message="shorturl.invalid_url")
     * @Assert\NotBlank()
     * @NotBannedDomain()
     */
    public $longUrl;
}
