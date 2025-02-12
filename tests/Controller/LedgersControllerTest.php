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
    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
    }

    public function testGetCollection1(): void
    {

        $client = static::createClient([], [
            'base_uri' => 'http://localhost:8080'
        ]);
        // Запрос к API
        $response = $client->request('GET', '/api/ledgers');


        // Проверяем статус ответа
        $this->assertResponseStatusCodeSame(200);


    }

    public function testGetCollection2(): void
    {

        $client = static::createClient([], [
            'base_uri' => 'http://localhost:8080'
        ]);
        $response = $client->request('GET', '/api/balances/8b05c363-79a2-4a12-bc5f-e2104852cb54');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

//        $this->assertJsonContains([
//            'ledgerId' => '8b05c363-79a2-4a12-bc5f-e2104852cb54',
//            'balance' => '111.00',
//            'currency' => 'EUR'
//        ]);
    }





    public function testGetBalanceNotFound(): void
    {
        $client = static::createClient([], [
            'base_uri' => 'http://localhost:8080'
        ]);

        $client->request('GET', '/api/balances/' . Uuid::v4());

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains(['error' => 'Balance not found']);
    }

    public function testCreateLedger(): void
    {
        //$client = $this->getClient();
        $client = static::createClient([], [
            'base_uri' => 'http://localhost:8080'
        ]);
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
