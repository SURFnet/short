<?php


namespace App\Exception;


final class ShortUrlNotFoundException extends \RuntimeException
{
    public static function becauseIsDeleted(): self
    {
        return new self('ShortUrl is already deleted');
    }
}
