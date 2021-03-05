<?php

namespace App\MessageHandler\User;

use App\Entity\User;
use App\Message\User\ProvideUserMessage;
use App\Repository\InstitutionRepository;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ProvideUserMessageHandler implements MessageHandlerInterface
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

    public function __invoke(ProvideUserMessage $message): User
    {
        $userId = $message->getId();
        $institutionHash = $message->getInstitutionHash();

        $user = $this->userRepository->find($userId);

        if (!$user instanceof User) {
            $user = User::create($userId);
            $this->userRepository->save($user);
        }

        if ($institution = $this->institutionRepository->findOneBy(['hash' => $institutionHash])) {
            $user->setInstitution($institution);
        }

        return $user;
    }
}
