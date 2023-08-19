<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\BookTestHelper;
use App\Tests\Traits\LibraryTestHelper;
use App\Tests\Traits\UserTestHelper;
use App\Tests\Traits\BaseTestHelper;

class BookControllerTest extends WebTestCase
{

    use BookTestHelper;
    use LibraryTestHelper;
    use UserTestHelper;
    use BaseTestHelper;

    private $client;
    private $libraryId;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $token = $this->getBearerToken($this->client);
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));
        $this->libraryId = $this->getLibraryId($this->client);
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/book/');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test successful scenario
     */
    public function testCreateOK(): int
    {
        $response = $this->createBook($this->client, $this->libraryId);

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
            "title" => ""
        ];

        $this->client->request('POST', '/book/', [], [], [], json_encode($data));
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * Test successful scenario
     * @depends testCreateOK
     */
    public function testShowOK($bookId): void
    {

        $this->client->request('GET', "/book/{$bookId}");
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test failure scenario
     */
    public function testShowKO(): void
    {

        $this->client->request('GET', '/book/' . $this->getLastIdPlusOne());
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test successful scenario
     * @depends testCreateOK
     */
    public function testUpdateOK($bookId): void
    {

        $this->client->request('PUT', "/book/{$bookId}", [], [], [], json_encode(['title' => 'newtitle', 'isbn' => '3210987654321']));
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test failure scenario
     */
    public function testUpdateKO(): void
    {

        $this->client->request('PUT', '/book/' . $this->getLastIdPlusOne(), [], [], [], json_encode(['title' => 'newtitle', 'isbn' => '3210987654321']));
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test successful scenario
     * @depends testCreateOK
     */
    public function testDeleteOK($bookId): void
    {

        $this->client->request('DELETE', "/book/{$bookId}");
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * Test failure scenario
     */
    public function testDeleteKO(): void
    {

        $this->client->request('DELETE', '/book/' . $this->getLastIdPlusOne());
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    private function getLastIdPlusOne(): int
    {
        $bookRepository = $this->client->getContainer()->get('doctrine')->getRepository(\App\Entity\Book::class);
        $lastId = $bookRepository->findMaxId();
        return $lastId + 1;
    }
}
