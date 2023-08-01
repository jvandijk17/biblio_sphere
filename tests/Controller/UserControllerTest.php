<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


class UserControllerTest extends WebTestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/user/');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test successful scenario
     */
    public function testCreateOK(): int
    {


        $data = [
            "first_name" => "John",
            "last_name" => "Doe",
            "email" => "john.doe@example.com",
            "password_hash" => "hashed_password",
            "address" => "123 Street",
            "city" => "City",
            "province" => "Province",
            "postal_code" => "12345",
            "registration_date" => "2023-07-20",
            "birth_date" => "1990-01-01",
            "library" => 2,
            "reputation" => 5,
            "blocked" => 0
        ];

        $this->client->request('POST', '/user/', [], [], [], json_encode($data));
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        return $responseData['id'];
    }

    /**
     * Test failure scenario
     */
    public function testCreateKO(): void
    {


        $data = [
            "first_name" => ""
        ];

        $this->client->request('POST', '/user/', [], [], [], json_encode($data));
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * Test successful scenario
     * @depends testCreateOK
     */
    public function testShowOK($userId): void
    {


        $this->client->request('GET', "/user/{$userId}");
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test failure scenario
     */
    public function testShowKO(): void
    {


        $this->client->request('GET', '/user/' . $this->getLastIdPlusOne());
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test successful scenario
     * @depends testCreateOK
     */
    public function testUpdateOK($userId): void
    {


        $this->client->request('PUT', "/user/{$userId}", [], [], [], json_encode(['username' => 'newuser', 'password' => 'newpassword']));
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test failure scenario
     */
    public function testUpdateKO(): void
    {


        $this->client->request('PUT', '/user/' . $this->getLastIdPlusOne(), [], [], [], json_encode(['username' => 'newuser', 'password' => 'newpassword']));
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test successful scenario
     * @depends testCreateOK
     */
    public function testDeleteOK($userId): void
    {


        $this->client->request('DELETE', "/user/{$userId}");
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * Test failure scenario
     */
    public function testDeleteKO(): void
    {


        $this->client->request('DELETE', '/user/' . $this->getLastIdPlusOne());
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    private function getLastIdPlusOne(): int
    {
        $userRepository = $this->client->getContainer()->get('doctrine')->getRepository(\App\Entity\User::class);
        $lastId = $userRepository->findMaxId();
        return $lastId + 1;
    }
}
