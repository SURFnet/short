<?php

namespace App\Message\ShortUrl;

final class CreateShortUrlMessage
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
     * @var string|null
     */
    private $shortUrl;

    public function __construct(string $ownerId, string $longUrl, ?string $shortUrl)
    {
        $this->ownerId = $ownerId;
        $this->longUrl = $longUrl;
        $this->shortUrl = $shortUrl;
    }

    /**
     * @return string
     */
    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    /**
     * @return string
     */
    public function getLongUrl(): string
    {
        return $this->longUrl;
    }

    /**
     * @return string|null
     */
    public function getShortUrl(): ?string
    {
        return $this->shortUrl;
    }
}
