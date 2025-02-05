<?php
// src/Controller/LedgersController.php
namespace App\Controller;

use App\Entity\Ledgers;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LedgersController extends AbstractController
{
    /**
     * @Route("/api/Ledgers", name="create_Ledgers", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Get the data from the request
        $data = json_decode($request->getContent(), true);

        
      

        if (isset($data['amount'], $data['Currency'])) {
            $currency = $entityManager->getRepository('CurrencyRepository')->findBy(['id'=>$data['Currency']]);
            
            // Create new Ledgers
            $Ledger = new Ledgers();
            $Ledger->setAmount($data['amount']);
            $Ledger->setFirstName($data['FirstName']);
            $Ledger->setLastName($data['LastName']);
            $Ledger->setCurrency($currency);

            // Persist the Ledger entity to the database
            $entityManager->persist($Ledger);
            $entityManager->flush();

            // Return a response with the created Ledger data
            return $this->json([
                'id' => $Ledger->getId(),
                'name' => $Ledger->getFirstName(),
                'price' => $Ledger->getAmount(),
                'uid' => $Ledger->getUuid(),
                'created' => $Ledger->getCreatedAt(),
            ], 201); // 201 Created
        }

        return $this->json([
            'error' => 'Invalid data.',
        ], 400); // 400 Bad Request
    }
}
