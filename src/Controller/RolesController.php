<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\RoleExtractorService;

#[Route("/roles", name: 'roles_')]
class RolesController extends AbstractController
{

    private $roleExtractorService;

    public function __construct(RoleExtractorService $roleExtractorService)
    {
        $this->roleExtractorService = $roleExtractorService;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $controllerDir = $this->getParameter('kernel.project_dir') . '/src/Controller';
        $roles = $this->roleExtractorService->extractRolesFromControllers($controllerDir);

        return $this->json($roles);
    }
}
