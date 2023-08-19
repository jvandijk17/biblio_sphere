<?php

namespace App\Tests\Traits;

use Symfony\Component\HttpFoundation\Response;

trait BaseTestHelper
{
    private function createLoginToken($client): Response
    {

        $client->request('POST', '/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => $_ENV['JWT_TEST_MAIL'],
            'password' => $_ENV['JWT_TEST_PASS']
        ]));

        $response = $client->getResponse();

        return $response;
    }

    private function getBearerToken($client): string
    {
        $response = $this->createLoginToken($client);
        $data = json_decode($response->getContent(), true);

        if (!isset($data['token'])) {
            throw new \Exception('Token not generated');
        }

        return $data['token'];
    }
}
