<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    private $userid;

    public function __construct(string $username)
    {
        $this->userid = $username;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->userid;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        // FIXME: need much better way to authorize admins.
        // or at the very least move this list to config
        if(in_array($this->userid, [
            'd55f67a470505f56d02b1be3a6321ea72fd3f539',
            '0d9f01ee321f2a480b6a296389887b66bfb65db2',
            'a8fb1916b4116b0e42ee1b991d37ffd66828b30c',
            '59cd042e0c992b73ae08e22b7fcae2b595adeaa1',
            'dd8660e3b354b811b87ef3d7e7cdb232dc8fd325',
	    '7235307d8f46a972d7c747a5584ce9662fa923a5',
            ] )) {
            return ['ROLE_USER', 'ROLE_ADMIN'];
	}

        return ['ROLE_USER'];
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
    }
}
