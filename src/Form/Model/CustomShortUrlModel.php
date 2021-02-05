<?php


namespace App\Form\Model;


use App\Validator\LongUrl;
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
     * @Assert\NotBlank()
     * @LongUrl()
     * @NotBannedDomain()
     */
    public $longUrl;
}
