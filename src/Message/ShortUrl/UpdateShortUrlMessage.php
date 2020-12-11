<?php

namespace App\Message\ShortUrl;

final class UpdateShortUrlMessage
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $longUrl;

    public function __construct(string $id, string $longUrl)
    {
        $this->id = $id;
        $this->longUrl = $longUrl;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLongUrl(): string
    {
        return $this->longUrl;
    }
}
