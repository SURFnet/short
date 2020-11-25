<?php

namespace App\Security\Voter;

use App\Entity\ShortUrl;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ShortUrlVoter extends Voter
{
    public const DELETE = 'DELETE';
    public const EDIT = 'EDIT';
    public const SHOW = 'SHOW';

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::SHOW])
            && $subject instanceof ShortUrl;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var $subject ShortUrl */

        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        return $subject->getOwner()->getUsername() === $user->getUsername();
    }
}
