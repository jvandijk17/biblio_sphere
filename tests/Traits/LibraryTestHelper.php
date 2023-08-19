<?php

namespace App\Tests\Traits;

use Symfony\Component\HttpFoundation\Response;

trait LibraryTestHelper
{
    private function createLibrary($client): Response
    {
        $data = [
            "name" => "Central Public Library",
            "address" => "456 Main Street",
            "city" => "Townsville",
            "province" => "Province",
            "postal_code" => "54321"
        ];
        $client->request('POST', '/library/', [], [], [], json_encode($data));
        return $client->getResponse();
    }

    private function getLibraryId($client): Response
    {
        $library = $this->createLibrary($client);
        $responseData = json_decode($library->getContent(), true);
        return $responseData['id'];
    }
}
