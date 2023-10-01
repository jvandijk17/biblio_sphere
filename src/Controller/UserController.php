<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Attribute\UserIsAdminOrOwner;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user', name: 'user_')]
class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;
    private UserService $userService;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, UserService $userService)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->userService = $userService;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function index(): JsonResponse
    {
        $users = $this->userRepository->findAll();
        return $this->json($users, Response::HTTP_OK, [], ['groups' => 'user']);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $this->denyAccessUnlessGranted(UserIsAdminOrOwner::class, $user);

        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user']);
    }

    #[Route('/{email}', name: 'show_by_email', methods: ['GET'])]
    public function show_by_email(string $email): JsonResponse
    {
        $userId = $this->userRepository->getUserIdByEmail($email);

        if ($userId != null) {
            return $this->show($userId);
        }

        return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->userService->saveUser(null, $data);

            return $this->json($user, Response::HTTP_CREATED, [], ['groups' => 'user']);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['errors' => json_decode($e->getMessage(), true)], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $this->denyAccessUnlessGranted(UserIsAdminOrOwner::class, $user);

        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->userService->saveUser($user, $data);

            return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user']);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['errors' => json_decode($e->getMessage(), true)], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $this->denyAccessUnlessGranted(UserIsAdminOrOwner::class, $user);

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User deleted'], Response::HTTP_NO_CONTENT);
    }
}
