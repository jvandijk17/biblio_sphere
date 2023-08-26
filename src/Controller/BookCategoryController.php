<?php

namespace App\Controller;

use App\Repository\BookCategoryRepository;
use App\Service\BookCategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route("/book/category", name: 'book_category_')]
class BookCategoryController extends AbstractController
{
    private BookCategoryRepository $bookCategoryRepository;
    private EntityManagerInterface $entityManager;
    private BookCategoryService $bookCategoryService;

    public function __construct(BookCategoryRepository $bookCategoryRepository, EntityManagerInterface $entityManager, BookCategoryService $bookCategoryService)
    {
        $this->bookCategoryRepository = $bookCategoryRepository;
        $this->entityManager = $entityManager;
        $this->bookCategoryService = $bookCategoryService;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $bookCategories = $this->bookCategoryRepository->findAll();
        return $this->json($bookCategories, Response::HTTP_OK, [], ['groups' => 'bookCategory']);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $bookCategory = $this->bookCategoryRepository->find($id);

        if (!$bookCategory) {
            return $this->json(['error' => 'Book category not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($bookCategory, Response::HTTP_FOUND, [], ['groups' => 'bookCategory']);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $bookCategory =  $this->bookCategoryService->saveBookCategory(null, $data);

            return $this->json($bookCategory, Response::HTTP_CREATED, [], ['groups' => 'bookCategory']);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[IsGranted("ROLE_ADMIN")]
    public function update(int $id, Request $request): JsonResponse
    {
        $bookCategory = $this->bookCategoryRepository->find($id);

        if (!$bookCategory) {
            return $this->json(['error' => 'Book category not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $data = json_decode($request->getContent(), true);
            $bookCategory = $this->bookCategoryService->saveBookCategory($bookCategory, $data);

            return $this->json($bookCategory, Response::HTTP_OK, [], ['groups' => 'bookCategory']);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(int $id): JsonResponse
    {
        $bookCategory = $this->bookCategoryRepository->find($id);

        if (!$bookCategory) {
            return $this->json(['error' => 'Book category not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($bookCategory);
        $this->entityManager->flush();

        return $this->json(['message' => 'Book category deleted'], Response::HTTP_NO_CONTENT);
    }
}
