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

    /**
     * @var string
     */
    private $label;

    public function __construct(string $id, string $longUrl, string $label = null)
    {
        $this->id = $id;
        $this->longUrl = $longUrl;
        $this->label = $label;
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

    /**
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }
}
