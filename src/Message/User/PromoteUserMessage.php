<?php

namespace App\Message\User;

final class PromoteUserMessage
{
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $role;

    public function __construct(string $username, string $role)
    {
        $this->username = $username;
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return strtoupper($this->role);
    }
}
