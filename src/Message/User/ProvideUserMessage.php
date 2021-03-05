<?php

namespace App\Message\User;

final class ProvideUserMessage
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string|null
     */
    private $institutionHash;

    public function __construct(string $id, string $institutionHash = null)
    {
        $this->id = $id;
        $this->institutionHash = $institutionHash;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getInstitutionHash(): ?string
    {
        return $this->institutionHash;
    }
}
