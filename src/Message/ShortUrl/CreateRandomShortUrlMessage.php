<?php

namespace App\Message\ShortUrl;

final class CreateRandomShortUrlMessage
{
    /**
     * @var string
     */
    private $ownerId;
    /**
     * @var string
     */
    private $longUrl;

    public function __construct(string $ownerId, string $longUrl)
    {
        $this->ownerId = $ownerId;
        $this->longUrl = $longUrl;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function getLongUrl(): string
    {
        return $this->longUrl;
    }
}
