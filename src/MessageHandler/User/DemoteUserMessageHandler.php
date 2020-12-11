<?php

namespace App\MessageHandler\User;

use App\Entity\User;
use App\Message\User\DemoteUserMessage;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

final class DemoteUserMessageHandler implements MessageHandlerInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(DemoteUserMessage $message): bool
    {
        $username = $message->getUsername();
        $role = $message->getRole();

        $user = $this->userRepository->find($username);

        if (!$user instanceof User) {
            throw new UsernameNotFoundException(sprintf("Username %s not found", $username));
        }

        $roles = $user->getRoles();
        if (false === $key = array_search($role, $roles, true)) {
            return false;
        }

        unset($roles[$key]);
        $user->setRoles($roles);

        return true;
    }
}
