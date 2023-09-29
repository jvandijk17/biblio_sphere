<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception->getMessage() === 'This user is blocked.') {
            $response = new Response();
            $response->setContent(json_encode(['error' => 'This user is blocked']));
            $response->setStatusCode(Response::HTTP_FORBIDDEN);

            $event->setResponse($response);
        }
    }
}
