<?php

namespace App\Entity;

use App\Validator\LongUrl;
use App\Validator\NotBannedDomain;
use App\Validator\NotForbiddenChars;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ShortUrl
 *
 * @ORM\Table(
 *     name="short_urls",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="short_url", columns={"short_url"})
 *     },
 *     indexes={
 *          @ORM\Index(columns={"owner", "created"}),
 *     }
 * )
 * @UniqueEntity(fields={"shortUrl"})
 * @ORM\Entity
 */
class ShortUrl
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="short_url", type="string", length=32, unique=true)
     * @NotForbiddenChars()
     */
    private $shortUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="long_url", type="text", length=65535)
     * @Assert\NotBlank()
     * @LongUrl()
     * @NotBannedDomain()
     */
    private $longUrl;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(nullable=false, name="owner")
     */
    private $owner;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime"))
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
     * @ORM\Column(name="clicks", type="integer")
     */
    private $clicks = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="text", length=255)
     */
    private $label;

    public function __construct()
    {
        $this->created = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getShortUrl(): ?string
    {
        return $this->shortUrl;
    }

    public function setShortUrl(string $shortUrl): self
    {
        $this->shortUrl = $shortUrl;

        return $this;
    }

    public function getLongUrl(): ?string
    {
        return $this->longUrl;
    }

    public function setLongUrl(string $longUrl): self
    {
        $this->longUrl = $longUrl;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label = null): self
    {
        $this->label = $label;

        return $this;
    }
}
