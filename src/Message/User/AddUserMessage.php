<?php

namespace App\Message\User;

final class AddUserMessage
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var array
     */
    private $roles;
    /**
     * @var int|null
     */
    private $institutionId;

    public function __construct(string $id, array $roles, ?int $institutionId = null)
    {
        $this->id = $id;
        $this->roles = $roles;
        $this->institutionId = $institutionId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return int|null
     */
    public function getInstitutionId(): ?int
    {
        return $this->institutionId;
    }
}
