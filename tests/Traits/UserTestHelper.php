<?php

namespace App\Tests\Traits;

use Symfony\Component\HttpFoundation\Response;

trait UserTestHelper
{
    private function createUser($client, $libraryId): Response
    {
        $data = [
            "first_name" => "John",
            "last_name" => "Doe",
            "email" => "john.doe@example.com",
            "password" => "hashed_password",
            "address" => "123 Street",
            "city" => "City",
            "province" => "Province",
            "postal_code" => "12345",
            "registration_date" => "2023-07-20",
            "birth_date" => "1990-01-01",
            "library" => $libraryId,
            "reputation" => 5,
            "blocked" => 0
        ];
        $client->request('POST', '/user/', [], [], [], json_encode($data));
        return $client->getResponse();
    }

    private function getUserId($client, $libraryId): Response
    {
        $user = $this->createUser($client, $libraryId);
        $responseData = json_decode($user->getContent(), true);
        return $responseData['id'];
    }
}
