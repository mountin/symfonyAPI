<?php


namespace App\Tests\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\Uid\Uuid;
use App\Entity\Ledgers;
use App\Entity\Currency;
use Doctrine\ORM\EntityManagerInterface;

class LedgersControllerTest extends ApiTestCase
{
    private EntityManagerInterface $entityManager;

    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', '/ledgers');
        // your assertions here...
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
    }

    public function testGetBalanceNotFound(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/balances/' . Uuid::v4());

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains(['error' => 'Balance not found']);
    }

    public function testCreateLedger(): void
    {
        $client = static::createClient();

        // Create Currency
        $currency = new Currency();
        $currency->setCode('USD');
        $currency->setId(2);
        $this->entityManager->persist($currency);
        $this->entityManager->flush();

        // Send request to create ledger
        $client->request('POST', '/api/balances', [
            'json' => [
                'amount' => '500.00',
                'currency' => $currency->getId(),
                'firstName' => 'John',
                'lastName' => 'Doe'
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'name' => 'John Doe',
            'price' => '500.00'
        ]);
    }
}
