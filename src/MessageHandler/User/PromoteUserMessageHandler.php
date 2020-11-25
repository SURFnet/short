<?php

namespace App\MessageHandler\User;

use App\Entity\User;
use App\Message\User\PromoteUserMessage;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

final class PromoteUserMessageHandler implements MessageHandlerInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(PromoteUserMessage $message): bool
    {
        $username = $message->getUsername();
        $role = $message->getRole();

        $user = $this->userRepository->find($username);

        if (!$user instanceof User) {
            throw new UsernameNotFoundException(sprintf("Username %s not found", $username));
        }

        $roles = $user->getRoles();
        if (in_array($role, $roles, true)) {
            return false;
        }

        $roles[] = $role;
        $user->setRoles($roles);

        return true;
    }
}
