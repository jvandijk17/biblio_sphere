<?php

namespace App\Service;

use Symfony\Component\Finder\Finder;

class ControllerLocator
{

    public function locate(string $controllerDir): \ArrayIterator
    {
        $finder = new Finder();

        $controllers = $finder->files()->in($controllerDir)->name('*Controller.php');
        return new \ArrayIterator(iterator_to_array($controllers));
    }
}
