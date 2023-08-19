<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\CategoryTestHelper;
use App\Tests\Traits\UserTestHelper;
use App\Tests\Traits\BaseTestHelper;
use App\Tests\Traits\LibraryTestHelper;

class CategoryControllerTest extends WebTestCase
{

    use CategoryTestHelper;
    use BaseTestHelper;
    use UserTestHelper;
    use LibraryTestHelper;

    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $token = $this->getBearerToken($this->client);
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/category/');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test successful scenario
     */
    public function testCreateOK(): int
    {
        $response = $this->createCategory($this->client);

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
            "name" => ""
        ];

        $this->client->request('POST', '/category/', [], [], [], json_encode($data));
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * Test successful scenario
     * @depends testCreateOK
     */
    public function testShowOK($categoryId): void
    {

        $this->client->request('GET', "/category/{$categoryId}");
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test failure scenario
     */
    public function testShowKO(): void
    {

        $this->client->request('GET', '/category/' . $this->getLastIdPlusOne());
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test successful scenario
     * @depends testCreateOK
     */
    public function testUpdateOK($categoryId): void
    {

        $this->client->request('PUT', "/category/{$categoryId}", [], [], [], json_encode(['name' => 'Drama']));
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test failure scenario
     */
    public function testUpdateKO(): void
    {

        $this->client->request('PUT', '/category/' . $this->getLastIdPlusOne(), [], [], [], json_encode(['name' => 'Drama']));
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test successful scenario
     * @depends testCreateOK
     */
    public function testDeleteOK($categoryId): void
    {

        $this->client->request('DELETE', "/category/{$categoryId}");
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * Test failure scenario
     */
    public function testDeleteKO(): void
    {

        $this->client->request('DELETE', '/category/' . $this->getLastIdPlusOne());
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    private function getLastIdPlusOne(): int
    {
        $categoryRepository = $this->client->getContainer()->get('doctrine')->getRepository(\App\Entity\Category::class);
        $lastId = $categoryRepository->findMaxId();
        return $lastId + 1;
    }
}
