<?php

namespace App\Tests\Traits;

use Symfony\Component\HttpFoundation\Response;

trait BaseTestHelper
{
    private function createLoginToken($client, $userId): Response
    {
        $email = $this->getUserEmailById($userId);
        $password = 'hashed_password';

        $client->request('POST', '/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => $email,
            'password' => $password
        ]));

        $response = $client->getResponse();

        return $response;
    }

    private function getBearerToken($client, $userId): string
    {
        $response = $this->createLoginToken($client, $userId);
        $data = json_decode($response->getContent(), true);

        if (!isset($data['token'])) {
            throw new \Exception('Token not generated');
        }

        return $data['token'];
    }

    private function getUserEmailById($userId)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $userRepository = $entityManager->getRepository(User::class);

        $user = $userRepository->find($userId);

        if (!$user) {
            throw new \Exception('User not found for the given ID');
        }

        return $user->getEmail();
    }
}
