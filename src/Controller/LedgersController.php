<?php
namespace App\Controller;

use App\Entity\Ledgers;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;



class LedgersController extends AbstractController
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


    #[Route('/api/balances/{id}', name: 'get_balances', methods: ['GET'])]
    public function getBalance(string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $ladgers = $entityManager->getRepository(Ledgers::class)->findBy(criteria: ['uuid' => $id]);

        if (!$ladgers) {
            return $this->json(['error' => 'Balance not found'], 404);
        }
        // Return only ledgerIds with data
        $data = array_map(function ($ladgers) {
            return [
                'ledgerId' => $ladgers->getUuid(),
                'balance' => $ladgers->getAmount(),
                'currency' => $ladgers->getCurrency(),
            ];
        }, $ladgers);

        return $this->json($data);
    }
    #[Route('/api/ledgers', name: 'new_ledger', methods: ['POST'])]
    public function newLedger(Request $request, CurrencyRepository $currencyRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Incorrect JSON'], 400);
        }

        if (isset($data['amount'], $data['currency'])) {
            $currency = $currencyRepository->findBy(['id'=>$data['currency']]);

            // Create new Ledgers
            $Ledger = new Ledgers();
            $Ledger->setAmount($data['amount'] ?? 0);
            $Ledger->setFirstName($data['firstName'] ?? '');
            $Ledger->setLastName($data['lastName'] ?? '');
            $Ledger->setCurrency($currency[0]);

            // validate
            $errors = $this->validator->validate($Ledger);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
                }
                return new JsonResponse(['errors' => $errorMessages], 400);
            }

            $this->entityManager->persist($Ledger);
            $this->entityManager->flush();

            // Return a response with the created Ledger data
            return $this->json([
                'uid' => $Ledger->getUuid(),
                'name' => $Ledger->getFirstName() . ' '. $Ledger->getLastName(),
                'amount' => $Ledger->getAmount(),
                'currency' => $Ledger->getCurrency(),
                'created' => $Ledger->getCreatedAt(),
            ], 201); // 201 Created
        }

        return $this->json([
            'error' => 'Invalid data.',
        ], 400); // 400 Bad Request
    }


}
