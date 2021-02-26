<?php

namespace App\MessageHandler\User;

use App\Entity\User;
use App\Message\User\AddUserMessage;
use App\Repository\InstitutionRepository;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AddUserMessageHandler implements MessageHandlerInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var InstitutionRepository
     */
    private $institutionRepository;

    public function __construct(UserRepository $userRepository, InstitutionRepository $institutionRepository)
    {
        $this->userRepository = $userRepository;
        $this->institutionRepository = $institutionRepository;
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

        if ($message->getInstitutionId()) {
            $institution = $this->institutionRepository->find($message->getInstitutionId());
            $user->setInstitution($institution);
        }

        $this->userRepository->save($user);

        return $user;
    }
}
