<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\LibraryTestHelper;
use App\Tests\Traits\BookTestHelper;
use App\Tests\Traits\CategoryTestHelper;
use App\Tests\Traits\UserTestHelper;
use App\Tests\Traits\BaseTestHelper;

class BookCategoryControllerTest extends WebTestCase
{

    use LibraryTestHelper;
    use BookTestHelper;
    use CategoryTestHelper;
    use BaseTestHelper;
    use UserTestHelper;

    private $client;
    private $bookId;
    private $categoryId;
    private $libraryId;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->libraryId = $this->getLibraryId($this->client);
        $this->bookId = $this->getBookId($this->client, $this->libraryId);
        $this->categoryId = $this->getCategoryId($this->client);
        $token = $this->getBearerToken($this->client, $this->getUserId($this->client, $this->libraryId));
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/book/category/');
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
            "book" => $this->bookId,
            "category" => $this->categoryId
        ];

        $this->client->request('POST', '/book/category/', [], [], [], json_encode($data));
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
            "book" => 0,
            "category" => 0,
        ];

        $this->client->request('POST', '/book/category/', [], [], [], json_encode($data));
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * Test successful scenario
     * @depends testCreateOK
     */
    public function testShowOK($bookCategoryId): void
    {

        $this->client->request('GET', "/book/category/{$bookCategoryId}");
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test failure scenario
     */
    public function testShowKO(): void
    {

        $this->client->request('GET', '/book/category/' . $this->getLastIdPlusOne());
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test successful scenario
     * @depends testCreateOK
     */
    public function testUpdateOK($bookCategoryId): void
    {
        $this->client->request('PUT', "/book/category/{$bookCategoryId}", [], [], [], json_encode(['loan_date' => '2023-07-25']));
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test failure scenario
     */
    public function testUpdateKO(): void
    {

        $this->client->request('PUT', '/book/category/' . $this->getLastIdPlusOne(), [], [], [], json_encode(['loan_date' => '2023-07-25']));
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * Test successful scenario
     * @depends testCreateOK
     */
    public function testDeleteOK($bookCategoryId): void
    {

        $this->client->request('DELETE', "/book/category/{$bookCategoryId}");
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * Test failure scenario
     */
    public function testDeleteKO(): void
    {

        $this->client->request('DELETE', '/book/category/' . $this->getLastIdPlusOne());
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    private function getLastIdPlusOne(): int
    {
        $bookRepository = $this->client->getContainer()->get('doctrine')->getRepository(\App\Entity\BookCategory::class);
        $lastId = $bookRepository->findMaxId();
        return $lastId + 1;
    }
}
