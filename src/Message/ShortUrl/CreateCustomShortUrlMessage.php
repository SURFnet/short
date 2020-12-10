<?php

namespace App\Message\ShortUrl;

final class CreateCustomShortUrlMessage
{
    /**
     * @var string
     */
    private $ownerId;
    /**
     * @var string
     */
    private $longUrl;
    /**
     * @var string
     */
    private $shorUrl;

    public function __construct(string $ownerId, string $longUrl, string $shorUrl)
    {
        $this->ownerId = $ownerId;
        $this->longUrl = $longUrl;
        $this->shorUrl = $shorUrl;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function getLongUrl(): string
    {
        return $this->longUrl;
    }

    public function getShorUrl(): string
    {
        return $this->shorUrl;
    }
}
