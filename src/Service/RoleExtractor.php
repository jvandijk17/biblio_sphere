<?php

namespace App\Service;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Finder\SplFileInfo;

use App\Service\ClassNameExtractor;

class RoleExtractor
{

    private ClassNameExtractor $classNameExtractor;

    public function __construct(ClassNameExtractor $classNameExtractor)
    {
        $this->classNameExtractor = $classNameExtractor;
    }

    /**
     * @param SplFileInfo $controllerFile
     * @return array
     */
    public function extractRolesFromController(SplFileInfo $controllerFile): array
    {
        $className = $this->classNameExtractor->extract($controllerFile);
        return $this->extractRolesFromClassName($className);
    }

    private function extractRolesFromClassName(string $className): array
    {
        $methodRoles = [];
        $reflectionClass = new \ReflectionClass($className);
        $controllerName = $reflectionClass->getShortName();

        foreach ($reflectionClass->getMethods() as $method) {
            $rolesForMethod = [];
            $attributes = $method->getAttributes(IsGranted::class);
            foreach ($attributes as $attribute) {
                $rolesForMethod[] = $attribute->getArguments()[0];
            }
            if ($rolesForMethod) {
                $methodRoles[$method->getName()] = $rolesForMethod;
            }
        }

        return [$controllerName => $methodRoles];
    }
}
