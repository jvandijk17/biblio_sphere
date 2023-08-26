<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Attribute\UserIsAdminOrOwner;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use \Symfony\Bundle\SecurityBundle\Security;



class UserIsAdminOrOwnerVoter extends Voter
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === UserIsAdminOrOwner::class && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $subject->getId() === $user->getId();
    }
}
