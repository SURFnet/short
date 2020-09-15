<?php

namespace App\Entity;

use App\Validator\NotBannedDomain;
use App\Validator\NotForbiddenChars;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ShortUrl
 *
 * @ORM\Table(name="short_urls", uniqueConstraints={@ORM\UniqueConstraint(name="short_url", columns={"short_url"})})
 * @ORM\Entity
 */
class ShortUrl
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="short_url", type="string", length=32, nullable=false)
     */
    private $shortUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="long_url", type="text", length=65535)
     * @Assert\Url()
     * @Assert\NotBlank()
     * @NotBannedDomain()
     */
    private $longUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="owner", type="string", length=64, nullable=false)
     */
    private $owner;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false))
     */
    private $created;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @var int
     *
     * @ORM\Column(name="clicks", type="integer", nullable=false)
     */
    private $clicks = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     */
    private $deleted = 0;

    public function __construct()
    {
        $this->created = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getShortUrl(): string
    {
        return $this->shortUrl;
    }

    public function setShortUrl(string $shortUrl): self
    {
        $this->shortUrl = $shortUrl;

        return $this;
    }

    public function getLongUrl(): string
    {
        return $this->longUrl;
    }

    public function setLongUrl(string $longUrl): self
    {
        $this->longUrl = $longUrl;

        return $this;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function setOwner(string $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCreated(): \DateTimeInterface
    {
        return $this->created;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(): self
    {
        $this->updated = new \DateTime("now");

        return $this;
    }

    public function getClicks(): int
    {
        return $this->clicks;
    }

    public function addClick(): self
    {
        $this->clicks++;

        return $this;
    }

    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }
}
