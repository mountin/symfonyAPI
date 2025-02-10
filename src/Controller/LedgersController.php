<?php
// src/Controller/LedgersController.php
namespace App\Controller;

use App\Entity\Ledgers;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
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

    public function __invoke(Request $request, CurrencyRepository $currencyRepository): JsonResponse
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

            // Persist the Ledger entity to the database
            $this->entityManager->persist($Ledger);
            $this->entityManager->flush();

            // Return a response with the created Ledger data
            return $this->json([
                'uid' => $Ledger->getUuid(),
                'name' => $Ledger->getFirstName() . ' '. $Ledger->getLastName(),
                'price' => $Ledger->getAmount(),
                'created' => $Ledger->getCreatedAt(),
            ], 201); // 201 Created
        }

        return $this->json([
            'error' => 'Invalid data.',
        ], 400); // 400 Bad Request
    }


}
