<?php

namespace App\Tests\Traits;

use Symfony\Component\HttpFoundation\Response;

trait CategoryTestHelper
{

    private function createCategory($client): Response
    {
        $data = [
            "name" => "Fiction"
        ];
        $client->request('POST', '/category/', [], [], [], json_encode($data));
        return $client->getResponse();
    }

    private function getCategoryId($client): Response
    {
        $category = $this->createCategory($client);
        $responseData = json_decode($category->getContent(), true);
        return $responseData['id'];
    }
}
