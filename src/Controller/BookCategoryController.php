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
        $groups = $this->getSerializationGroups();
        $bookCategories = $this->bookCategoryRepository->findAll();
        return $this->json($bookCategories, Response::HTTP_OK, [], ['groups' => $groups]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $bookCategory = $this->bookCategoryRepository->find($id);

        if (!$bookCategory) {
            return $this->errorResponse('Book category not found');
        }

        $groups = $this->getSerializationGroups();
        return $this->json($bookCategory, Response::HTTP_FOUND, [], ['groups' => $groups]);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function create(Request $request): JsonResponse
    {
        return $this->saveOrUpdateBookCategory(null, $request);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[IsGranted("ROLE_ADMIN")]
    public function update(int $id, Request $request): JsonResponse
    {
        return $this->saveOrUpdateBookCategory($id, $request);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(int $id): JsonResponse
    {
        $bookCategory = $this->bookCategoryRepository->find($id);

        if (!$bookCategory) {
            return $this->errorResponse('Book category not found');
        }

        $this->entityManager->remove($bookCategory);
        $this->entityManager->flush();

        return $this->json(['message' => 'Book category deleted'], Response::HTTP_NO_CONTENT);
    }

    private function saveOrUpdateBookCategory(?int $id, Request $request): JsonResponse
    {
        $bookCategory = $id ? $this->bookCategoryRepository->find($id) : null;

        if ($id && !$bookCategory) {
            return $this->errorResponse('Book category not found');
        }

        try {
            $data = json_decode($request->getContent(), true);
            $bookCategory = $this->bookCategoryService->saveBookCategory($bookCategory, $data);

            return $this->json($bookCategory, $id ? Response::HTTP_OK : Response::HTTP_CREATED, [], ['groups' => 'bookCategory']);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    private function getSerializationGroups(): array
    {
        $groups = ['bookCategory'];

        if ($this->isGranted('ROLE_ADMIN')) {
            $groups[] = 'bookCategory_secret';
        }

        return $groups;
    }

    private function errorResponse(string $message, int $status = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return $this->json(['error' => $message], $status);
    }
}
