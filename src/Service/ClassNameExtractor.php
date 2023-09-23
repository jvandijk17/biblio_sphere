<?php

namespace App\Service;

use Symfony\Component\Finder\SplFileInfo;

class ClassNameExtractor
{
    public function extract(SplFileInfo $controllerFile): string
    {
        return 'App\\Controller\\' . $controllerFile->getBasename('.php');
    }
}
