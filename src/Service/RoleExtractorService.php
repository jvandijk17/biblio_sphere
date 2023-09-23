<?php

namespace App\Service;

class RoleExtractorService
{

    private ControllerLocator $controllerLocator;
    private RoleExtractor $roleExtractor;

    public function __construct(
        ControllerLocator $controllerLocator,
        RoleExtractor $roleExtractor
    ) {
        $this->controllerLocator = $controllerLocator;
        $this->roleExtractor = $roleExtractor;
    }

    public function extractRolesFromControllers(string $controllerDir): array
    {
        $rolesMap = [];
        $files = $this->controllerLocator->locate($controllerDir);

        foreach ($files as $file) {
            $controllerRoles = $this->roleExtractor->extractRolesFromController($file);
            $rolesMap = array_merge($rolesMap, $controllerRoles);            
        }

        return $rolesMap;
    }
}
