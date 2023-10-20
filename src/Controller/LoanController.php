<?php

namespace App\Controller;

use App\Repository\LoanRepository;
use App\Repository\UserRepository;
use App\Service\LoanService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/loan', name: 'loan_')]
class LoanController extends AbstractController
{
    private LoanRepository $loanRepository;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private LoanService $loanService;

    public function __construct(LoanRepository $loanRepository, EntityManagerInterface $entityManager, LoanService $loanService, UserRepository $userRepository)
    {
        $this->loanRepository = $loanRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->loanService = $loanService;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function index(): JsonResponse
    {
        $loans = $this->loanRepository->findAll();
        return $this->json($loans, Response::HTTP_OK, [], ['groups' => $this->getSerializationGroups()]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $loan = $this->loanRepository->find($id);

        if (!$loan) {
            return $this->errorResponse('Loan not found');
        }

        return $this->json($loan, Response::HTTP_OK, [], ['groups' => $this->getSerializationGroups()]);
    }

    #[Route('/by-user/{userId}', name: 'by_user', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function getByUserId(int $userId): JsonResponse
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return $this->errorResponse('User not found', Response::HTTP_NOT_FOUND);
        }

        $loans = $this->loanRepository->findBy(['user' => $userId, 'return_date' => null]);

        return $this->json($loans, Response::HTTP_OK, [], ['groups' => $this->getSerializationGroups()]);
    }




    #[Route('/', name: 'create', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function create(Request $request): JsonResponse
    {
        return $this->saveOrUpdateLoan(null, $request);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[IsGranted("ROLE_ADMIN")]
    public function update(int $id, Request $request): JsonResponse
    {
        return $this->saveOrUpdateLoan($id, $request);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(int $id): JsonResponse
    {
        $loan = $this->loanRepository->find($id);

        if (!$loan) {
            return $this->errorResponse('Loan not found');
        }

        $this->entityManager->remove($loan);
        $this->entityManager->flush();

        return $this->json(['message' => 'Loan deleted'], Response::HTTP_NO_CONTENT);
    }

    private function saveOrUpdateLoan(?int $id, Request $request): JsonResponse
    {
        $loan = $id ? $this->loanRepository->find($id) : null;

        if ($id && !$loan) {
            return $this->errorResponse('Loan not found');
        }

        try {
            $data = json_decode($request->getContent(), true);
            $loan = $this->loanService->saveLoan($loan, $data);

            return $this->json($loan, $id ? Response::HTTP_OK : Response::HTTP_CREATED, [], ['groups' => 'loan']);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    private function getSerializationGroups(): array
    {
        $groups = ['loan'];

        if ($this->isGranted('ROLE_ADMIN')) {
            $groups[] = 'loan_secret';
        }

        return $groups;
    }

    private function errorResponse(string $message, int $status = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return $this->json(['error' => $message], $status);
    }
}
