<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function loadUserByUsername(string $username): UserInterface
    {
	return new User($username);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
	return $user;
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}
