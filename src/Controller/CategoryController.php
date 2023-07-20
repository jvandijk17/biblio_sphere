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

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{

    private CategoryRepository $categoryRepository;
    private EntityManagerInterface $entityManager;
    private CategoryService $categoryService;

    public function __construct(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager, CategoryService $categoryService) {
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
        $this->categoryService = $categoryService;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $categories = $this->categoryRepository->findAll();
        return $this->json($categories, Response::HTTP_OK, [], ['groups' => 'category']);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryRepository->find($id);

        if(!$category) {
            return $this->json(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($category, Response::HTTP_FOUND, [], ['groups' => 'category']);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $category = $this->categoryService->createCategory($data);

            return $this->json($category, Response::HTTP_CREATED, [], ['groups' => 'category']);
        } catch(\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $category = $this->categoryRepository->find($id);

        if(!$category) {
            return $this->json(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $data = json_decode($request->getContent(), true);
            $category = $this->categoryService->updateCategory($category, $data);

            return $this->json($category, Response::HTTP_OK, [], ['groups' => 'category']);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $category = $this->categoryRepository->find($id);

        if(!$category) {
            return $this->json(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->json(['message' => 'Category deleted'], Response::HTTP_NO_CONTENT);
    }

}
