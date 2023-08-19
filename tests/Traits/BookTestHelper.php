<?php

namespace App\Tests\Traits;

use Symfony\Component\HttpFoundation\Response;

trait BookTestHelper
{

    private function createBook($client, $libraryId): Response
    {
        $data = [
            "title" => "Book Title",
            "author" => "Book Author",
            "publisher" => "Book Publisher",
            "isbn" => "1234567890123",
            "publication_year" => "2023-01-01",
            "page_count" => 500,
            "library_id" => $libraryId
        ];
        $client->request('POST', '/book/', [], [], [], json_encode($data));
        return $client->getResponse();
    }

    private function getBookId($client, $libraryId): int
    {
        $book = $this->createBook($client, $libraryId);
        $responseData = json_decode($book->getContent(), true);
        return $responseData['id'];
    }
}
