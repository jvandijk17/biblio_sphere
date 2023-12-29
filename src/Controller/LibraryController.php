<?php

namespace App\Controller;

use App\Repository\LibraryRepository;
use App\Service\LibraryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    #[Route('/preview_libraries', name: 'preview_libraries', methods: ['GET'])]
    public function publicLibraries(): JsonResponse
    {
        $libraries = $this->libraryRepository->findAll();
        return $this->json($libraries, Response::HTTP_OK, [], ['groups' => 'preview_library']);
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $groups = $this->getSerializationGroups();
        $libraries = $this->libraryRepository->findAll();
        return $this->json($libraries, Response::HTTP_OK, [], ['groups' => $groups]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        $library = $this->libraryRepository->find($id);

        if (!$library) {
            return $this->errorResponse('Library not found');
        }

        $groups = $this->getSerializationGroups();
        return $this->json($library, Response::HTTP_OK, [], ['groups' => $groups]);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function create(Request $request): JsonResponse
    {
        return $this->saveOrUpdateLibrary(null, $request);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[IsGranted("ROLE_ADMIN")]
    public function update(int $id, Request $request): JsonResponse
    {
        return $this->saveOrUpdateLibrary($id, $request);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(int $id): JsonResponse
    {
        $library = $this->libraryRepository->find($id);

        if (!$library) {
            return $this->errorResponse('Library not found');
        }

        $this->entityManager->remove($library);
        $this->entityManager->flush();

        return $this->json(['message' => 'Library deleted'], Response::HTTP_NO_CONTENT);
    }

    private function saveOrUpdateLibrary(?int $id, Request $request): JsonResponse
    {
        $library = $id ? $this->libraryRepository->find($id) : null;

        if ($id && !$library) {
            return $this->errorResponse('Library not found');
        }

        try {
            $data = json_decode($request->getContent(), true);
            $library = $this->libraryService->saveLibrary($library, $data);

            return $this->json($library, $id ? Response::HTTP_OK : Response::HTTP_CREATED, [], ['groups' => 'library']);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    private function getSerializationGroups(): array
    {
        $groups = ['library'];

        if ($this->isGranted('ROLE_ADMIN')) {
            $groups[] = 'library_secret';
        }

        return $groups;
    }

    private function errorResponse(string $message, int $status = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return $this->json(['error' => $message], $status);
    }
}
