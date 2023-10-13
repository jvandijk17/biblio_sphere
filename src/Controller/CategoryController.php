<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    private CategoryRepository $categoryRepository;
    private EntityManagerInterface $entityManager;
    private CategoryService $categoryService;

    public function __construct(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager, CategoryService $categoryService)
    {
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
        $this->categoryService = $categoryService;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $groups = $this->getSerializationGroups();
        $categories = $this->categoryRepository->findAll();
        return $this->json($categories, Response::HTTP_OK, [], ['groups' => $groups]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryRepository->find($id);

        if (!$category) {
            return $this->errorResponse('Category not found');
        }

        $groups = $this->getSerializationGroups();
        return $this->json($category, Response::HTTP_FOUND, [], ['groups' => $groups]);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function create(Request $request): JsonResponse
    {
        return $this->saveOrUpdateCategory(null, $request);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[IsGranted("ROLE_ADMIN")]
    public function update(int $id, Request $request): JsonResponse
    {
        return $this->saveOrUpdateCategory($id, $request);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(int $id): JsonResponse
    {
        $category = $this->categoryRepository->find($id);

        if (!$category) {
            return $this->errorResponse('Category not found');
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->json(['message' => 'Category deleted'], Response::HTTP_NO_CONTENT);
    }

    private function saveOrUpdateCategory(?int $id, Request $request): JsonResponse
    {
        $category = $id ? $this->categoryRepository->find($id) : null;

        if ($id && !$category) {
            return $this->errorResponse('Category not found');
        }

        try {
            $data = json_decode($request->getContent(), true);
            $category = $this->categoryService->saveCategory($category, $data);

            return $this->json($category, $id ? Response::HTTP_OK : Response::HTTP_CREATED, [], ['groups' => 'category']);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    private function getSerializationGroups(): array
    {
        $groups = ['category'];

        if ($this->isGranted('ROLE_ADMIN')) {
            $groups[] = 'category_secret';
        }

        return $groups;
    }

    private function errorResponse(string $message, int $status = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return $this->json(['error' => $message], $status);
    }
}
