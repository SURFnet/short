<?php

namespace App\MessageHandler\User;

use App\Entity\User;
use App\Message\User\ProvideUserMessage;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ProvideUserMessageHandler implements MessageHandlerInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(ProvideUserMessage $message): User
    {
        $user = $this->userRepository->find($message->getId());

        if (!$user instanceof User) {
            $user = User::create($message->getId());
            $this->userRepository->save($user);
        }

        return $user;
    }
}
