<?php

namespace App\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTCreatedSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_jwt_creation' => 'onJWTCreated',
        ];
    }

    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();

        if(method_exists($user, 'getRoles')) {
            $payload = $event->getData();
            $payload['roles'] = $user->getRoles();

            $event->setData($payload);
        }
    }

}
