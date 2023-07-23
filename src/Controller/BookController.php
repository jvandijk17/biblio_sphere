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
        $books = $this->bookRepository->findAll();
        return $this->json($books, Response::HTTP_OK, [], ['groups' => 'book']);
    }
    
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $book = $this->bookRepository->find($id);

        if(!$book) {
            return $this->json(['error' => 'Book not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($book, Response::HTTP_FOUND, [], ['groups' => 'book']);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $book = $this->bookService->createBook($data);
            
            return $this->json($book, Response::HTTP_CREATED, [], ['groups' => 'book']);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $book = $this->bookRepository->find($id);

        if(!$book) {
            return $this->json(['error' => 'Book not found'], Response::HTTP_NOT_FOUND);
        }
        
        try {
            $data = json_decode($request->getContent(), true);
            $book = $this->bookService->updateBook($book, $data);

            return $this->json($book, Response::HTTP_OK, [], ['groups' => 'book']);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $book = $this->bookRepository->find($id);

        if(!$book) {
            return $this->json(['error' => 'Book not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();

        return $this->json(['message' => 'Book deleted'], Response::HTTP_NO_CONTENT);
    }
}
