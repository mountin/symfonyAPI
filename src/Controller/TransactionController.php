<?php

namespace App\Controller;

use App\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Ledgers;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class TransactionController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private CurrencyRepository $currencyRepository;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator, CurrencyRepository $currencyRepository)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->currencyRepository = $currencyRepository;
    }

    // transaction type (debit/credit), amount, currency, and a unique transaction ID
    #[Route('/api/transactions', name: 'new_transaction', methods: ['POST'])]
    public function newTransaction(Request $request, CurrencyRepository $currencyRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Incorrect JSON'], 400);
        }

        if (!isset($data['amount'], $data['transactionID'], $data['currency'], $data['ledgerID'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 401);
        }

        $currency = $currencyRepository->findOneBy(['id'=>$data['currency']]);
        if (!$currency) {
              return new JsonResponse(['error' => 'Currency not found'], 404);
         }

        $ledgerID = $this->entityManager->getRepository(Ledgers::class)->findOneBy(
            ['uuid'=>$data['ledgerID'],
            'currency' => $currency]
        );
        if (!$ledgerID) {
            return new JsonResponse(['error' => 'Ledger not found'], 404);
        }

        if (!in_array($data['type'], ['debit', 'credit'])) {
            return new JsonResponse(['error' => 'Invalid transaction type'], 400);
        }
        if ($data['type'] === 'debit' && $ledgerID->getAmount() < $data['amount']) {
            return new JsonResponse(['error' => 'Insufficient balance'], 400);
        }

            // Create new Transaction
            $transaction = new Transaction();
            $transaction->setAmount($data['amount'] ?? 0);
            $transaction->setLedger($ledgerID ?? 0);
            $transaction->setCurrency($currency);
            $transaction->setTransactionId($data['transactionID'] ?? 0);
            $transaction->setType($data['type']);

            // Validate entity
            $errors = $this->validator->validate($transaction);
            if (count($errors) > 0) {
                return new JsonResponse(['error' => (string) $errors], 400);
            }

            // Update ledger balance
            $ledgerID->updateBalance($data['type'], $data['amount']);

            // Persist changes
            $this->entityManager->persist($transaction);
            $this->entityManager->flush();
            $this->entityManager->persist($ledgerID);


            return new JsonResponse([
                'transactionID' => $transaction->getTransactionId(),
                'ledgerID' => $transaction->getLedger()->getUuid(),
                'type' => $transaction->getType(),
                'currency' => $transaction->getCurrency()->getCode(),
                'newAmount' => $ledgerID->getAmount(),
            ], 201);

    }
}
