<?php

namespace App\Message\User;

final class AddUserMessage
{
    /*
     * Add whatever properties & methods you need to hold the
     * data for this message class.
     */

    /**
     * @var string
     */
    private $id;
    /**
     * @var array
     */
    private $roles;

    public function __construct(string $id, array $roles)
    {
        $this->id = $id;
        $this->roles = $roles;
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
}
