<?php


namespace App\Exception;


final class ShortCodeNotAvailableException extends \RuntimeException
{
    public static function with(string $code): self
    {
        return new self(sprintf('Code "%s" is not available', $code));
    }

    public static function becauseTooManyTries(): self
    {
        return new self('Too many tries to create shortcode');
    }
}
