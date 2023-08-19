<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\LibraryTestHelper;
use App\Tests\Traits\UserTestHelper;
use App\Tests\Traits\BaseTestHelper;


class LibraryControllerTest extends WebTestCase
{

    use LibraryTestHelper;
    use UserTestHelper;
    use BaseTestHelper;

    private $client;
    private $userId;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $token = $this->getBearerToken($this->client, $this->getUserId($this->client, $this->getLibraryId($this->client)));
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/library/');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test successful scenario
     */
    public function testCreateOK(): int
    {
        $response = $this->createLibrary($this->client);

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
            "province" => "Paris"
        ];

        $this->client->request('POST', '/library/', [], [], [], json_encode($data));
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * Test successful scenario
     * @depends testCreateOK
     */
    public function testShowOK($libraryId): void
    {


        $this->client->request('GET', "/library/{$libraryId}");
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test failure scenario
     */
    public function testShowKO(): void
    {


        $this->client->request('GET', '/library/' . $this->getLastIdPlusOne());
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test successful scenario
     * @depends testCreateOK
     */
    public function testUpdateOK($libraryId): void
    {


        $this->client->request('PUT', "/library/{$libraryId}", [], [], [], json_encode(['city' => 'Paris']));
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test failure scenario
     */
    public function testUpdateKO(): void
    {


        $this->client->request('PUT', '/library/' . $this->getLastIdPlusOne(), [], [], [], json_encode(['city' => 'Paris']));
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test successful scenario
     * @depends testCreateOK
     */
    public function testDeleteOK($libraryId): void
    {


        $this->client->request('DELETE', "/library/{$libraryId}");
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * Test failure scenario
     */
    public function testDeleteKO(): void
    {


        $this->client->request('DELETE', '/library/' . $this->getLastIdPlusOne());
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    private function getLastIdPlusOne(): int
    {
        $libraryRepository = $this->client->getContainer()->get('doctrine')->getRepository(\App\Entity\Library::class);
        $lastId = $libraryRepository->findMaxId();
        return $lastId + 1;
    }
}
