<?php


namespace App\Tests\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Currency;
use App\Entity\Ledgers;
use App\Entity\Transaction;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class TransactionControllerTest extends ApiTestCase
{
    private EntityManagerInterface $entityManager;
    public static String $baseUrl = 'http://localhost:8080';

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
    }

    public function testCreateTransactionSuccess(): void
    {

        $client = static::createClient([], [
            'base_uri' => self::$baseUrl
        ]);

        // Create Currency
        $currency = $this->entityManager->getRepository(Currency::class)->findOneBy(criteria: ['id' => 1]);

        // Create Ledger
        $ledger = new Ledgers();
        $ledger->setAmount('1000.00'); // Initial balance
        $ledger->setCurrency($currency);
        $ledger->setFirstName('John2');
        $ledger->setLastName('Doe2');


        $this->entityManager->persist($ledger);
        $this->entityManager->flush();
        $uuid = $ledger->getUuid()->toString();


        // Make the API Request
        $response = $client->request('POST', '/api/transactions', [
            'json' => [
                'ledgerID' => $ledger->getUuid()->toString(),
                'type' => 'credit',
                'amount' => '500.00',
                'currency' => $currency->getId(),
                'transactionID' => 'txn_12345'
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'ledgerID' => $ledger->getUuid()->toString(),
            'type' => 'credit',
            'currency' => $currency->getCode(),
            'newAmount' => '1500.00', // Balance after debit
            'transactionID' => 'txn_12345'
        ]);
        //dd('RESPONCE');
    }

    public function testCreateTransactionWithMissingFields(): void
    {
        $client = static::createClient();

        $client->request('POST', '/transactions', [
            'json' => [
                'type' => 'debit',
                'amount' => '500.00',
                'currency' => 1, // Assume currency exists
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains(['error' => 'Missing required fields']);
    }

    public function testCreateTransactionWithInvalidType(): void
    {
        $client = static::createClient();

        $client->request('POST', '/transactions', [
            'json' => [
                'ledgerID' => Uuid::v4(),
                'type' => 'invalid_type',
                'amount' => '100.00',
                'currency' => 1,
                'transactionID' => 'txn_12346'
            ],
        ]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains(['error' => 'Invalid transaction type']);
    }

    public function testCreateTransactionWithInsufficientBalance(): void
    {
        $client = static::createClient();

        // Create Currency
        $currency = new Currency();
        $currency->setCode('EUR');
        $currency->setId(1);
        $this->entityManager->persist($currency);

        // Create Ledger with low balance
        $ledger = new Ledgers();
        $ledger->setAmount('100.00'); // Small balance
        $ledger->setCurrency($currency);


        $this->entityManager->persist($ledger);
        $this->entityManager->flush();

        // Attempt to debit more than available balance
        $client->request('POST', '/api/transactions', [
            'json' => [
                'ledgerID' => $ledger->getUuid(),
                'type' => 'debit',
                'amount' => '200.00', // Greater than balance
                'currency' => $currency->getId(),
                'transactionID' => 'txn_12347'
            ],
        ]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains(['error' => 'Insufficient balance']);
    }

    public function testCreateTransactionWithNonExistingLedger(): void
    {
        $client = static::createClient();

        $client->request('POST', '/transactions', [
            'json' => [
                'ledgerID' => Uuid::v4(), // Non-existing Ledger ID
                'type' => 'credit',
                'amount' => '300.00',
                'currency' => 1,
                'transactionID' => 'txn_12348'
            ],
        ]);

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains(['error' => 'Ledger not found']);
    }
}
