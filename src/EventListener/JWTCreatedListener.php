<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use App\Repository\UserRepository;

class JWTCreatedListener
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();

        if ($user && $this->userRepository->isUserBlockedByEmail($user->getUserIdentifier())) {
            throw new \Exception('This user is blocked.');
        }
    }
}
