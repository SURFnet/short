<?php

namespace App\MessageHandler\User;

use App\Entity\User;
use App\Message\User\AddUserMessage;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AddUserMessageHandler implements MessageHandlerInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(AddUserMessage $message): User
    {
        $id = $message->getId();
        $roles = $message->getRoles();

        if ($this->userRepository->find($id) instanceof User) {
            throw new \RuntimeException('User id already exists');
        }

        $user = User::create($id);
        $user->setRoles($roles);

        $this->userRepository->save($user);

        return $user;
    }
}
