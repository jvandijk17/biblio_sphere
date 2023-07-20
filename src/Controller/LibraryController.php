<?php

namespace App\Controller;

use App\Repository\LibraryRepository;
use App\Service\LibraryService;
use Doctrine\ORM\EntityManagerInterface;
use LDAP\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/library', name: 'library_')]
class LibraryController extends AbstractController
{
    private LibraryRepository $libraryRepository;
    private EntityManagerInterface $entityManager;
    private LibraryService $libraryService;    

    public function __construct(LibraryRepository $libraryRepository, EntityManagerInterface $entityManager, LibraryService $libraryService)
    {
        $this->libraryRepository = $libraryRepository;
        $this->entityManager = $entityManager;
        $this->libraryService = $libraryService;        
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $libraries = $this->libraryRepository->findAll();
        return $this->json($libraries, Response::HTTP_OK, [], ['groups' => 'library']);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $library = $this->libraryRepository->find($id);

        if (!$library) {
            return $this->json(['error' => 'Library not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($library, Response::HTTP_FOUND, [], ['groups' => 'library']);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $library = $this->libraryService->createLibrary($data);

            return $this->json($library, Response::HTTP_CREATED, [], ['groups' => 'library']);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $library = $this->libraryRepository->find($id);

        if (!$library) {
            return $this->json(['error' => 'Library not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $data = json_decode($request->getContent(), true);
            $library = $this->libraryService->updateLibrary($library, $data);

            return $this->json($library, Response::HTTP_OK, [], ['groups' => 'library']);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $library = $this->libraryRepository->find($id);

        if (!$library) {
            return $this->json(['error' => 'Library not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($library);
        $this->entityManager->flush();

        return $this->json(['message' => 'Library deleted'], Response::HTTP_NO_CONTENT);
    }
}
