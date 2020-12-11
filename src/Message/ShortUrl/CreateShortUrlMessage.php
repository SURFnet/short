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
    private $shorUrl;

    public function __construct(string $ownerId, string $longUrl, ?string $shorUrl)
    {
        $this->ownerId = $ownerId;
        $this->longUrl = $longUrl;
        $this->shorUrl = $shorUrl;
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
    public function getShorUrl(): ?string
    {
        return $this->shorUrl;
    }
}
