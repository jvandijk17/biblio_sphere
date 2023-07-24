<?php

namespace App\Controller;

use App\Repository\LoanRepository;
use App\Service\LoanService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/loan', name: 'loan')]
class LoanController extends AbstractController
{
    private LoanRepository $loanRepository;
    private EntityManagerInterface $entityManager;
    private LoanService $loanService;

    public function __construct(LoanRepository $loanRepository, EntityManagerInterface $entityManager, LoanService $loanService)
    {
        $this->loanRepository = $loanRepository;
        $this->entityManager = $entityManager;
        $this->loanService = $loanService;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $loans = $this->loanRepository->findAll();
        return $this->json($loans, Response::HTTP_OK, [], ['groups' => 'loan']);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $loan = $this->loanRepository->find($id);

        if(!$loan) {
            return $this->json(['error' => 'Loan not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($loan, Response::HTTP_FOUND, [], ['groups' => 'loan']);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $loan = $this->loanService->saveLoan(null, $data);

            return $this->json($loan, Response::HTTP_CREATED, [], ['groups' => 'loan']);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $loan = $this->loanRepository->find($id);

        if (!$loan) {
            return $this->json(['error' => 'Loan not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $data = json_decode($request->getContent(), true);
            $loan = $this->loanService->saveLoan($loan, $data);

            return $this->json($loan, Response::HTTP_OK, [], ['groups' => 'loan']);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $loan = $this->loanRepository->find($id);

        if(!$loan) {
            return $this->json(['error' => 'Loan not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($loan);
        $this->entityManager->flush();

        return $this->json(['message' => 'Loan deleted'], Response::HTTP_NO_CONTENT);
    }
}
