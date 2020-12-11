<?php


namespace App\Exception;


final class ShortCodeNotAvailableException extends \RuntimeException
{
    public static function becauseAlreadyUsed(): self
    {
        return new self('This value is already used.');
    }

    public static function becauseTooManyTries(): self
    {
        return new self('Too many tries to create shortcode');
    }
}
