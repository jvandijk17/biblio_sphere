<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Service\BookService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route("/book", name: 'book_')]
class BookController extends AbstractController
{
    private BookRepository $bookRepository;
    private EntityManagerInterface $entityManager;
    private BookService $bookService;

    public function __construct(BookRepository $bookRepository, EntityManagerInterface $entityManager, BookService $bookService)
    {
        $this->bookRepository = $bookRepository;
        $this->entityManager = $entityManager;
        $this->bookService = $bookService;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $groups = $this->getSerializationGroups();
        $books = $this->bookRepository->findAll();
        return $this->json($books, Response::HTTP_OK, [], ['groups' => $groups]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $book = $this->bookRepository->find($id);

        if (!$book) {
            return $this->errorResponse('Book not found');
        }

        $groups = $this->getSerializationGroups();
        return $this->json($book, Response::HTTP_OK, [], ['groups' => $groups]);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function create(Request $request): JsonResponse
    {
        return $this->saveOrUpdateBook(null, $request);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[IsGranted("ROLE_ADMIN")]
    public function update(int $id, Request $request): JsonResponse
    {
        return $this->saveOrUpdateBook($id, $request);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(int $id): JsonResponse
    {
        $book = $this->bookRepository->find($id);

        if (!$book) {
            return $this->errorResponse('Book not found');
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();

        return $this->json(['message' => 'Book deleted'], Response::HTTP_NO_CONTENT);
    }

    private function saveOrUpdateBook(?int $id, Request $request): JsonResponse
    {
        $book = $id ? $this->bookRepository->find($id) : null;

        if ($id && !$book) {
            return $this->errorResponse('Book not found');
        }

        try {
            $data = json_decode($request->getContent(), true);
            $book = $this->bookService->saveBook($book, $data);
            $groups = $this->getSerializationGroups();

            return $this->json($book, $id ? Response::HTTP_OK : Response::HTTP_CREATED, [], ['groups' => $groups]);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    private function getSerializationGroups(): array
    {
        $groups = ['book'];

        if ($this->isGranted('ROLE_ADMIN')) {
            $groups[] = 'book_secret';
        }

        return $groups;
    }

    private function errorResponse(string $message, int $status = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return $this->json(['error' => $message], $status);
    }
}
