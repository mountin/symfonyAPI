<?php
namespace App\Tests\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Repository\CurrencyRepository;
use Symfony\Component\Uid\Uuid;
use App\Entity\Ledgers;
use App\Entity\Currency;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class LedgersControllerTest extends ApiTestCase
{

    private EntityManagerInterface $entityManager;
    public static String $baseUrl = 'http://localhost:8080';

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
    }

    public function testGetCollection1(): void
    {
        $client = static::createClient([], [
            'base_uri' => self::$baseUrl
        ]);
        // Запрос к API
        $response = $client->request('GET', '/api/ledgers');


        // Проверяем статус ответа
        $this->assertResponseStatusCodeSame(200);

    }

    public function testGetCollection2(): void
    {

        $client = static::createClient([], [
            'base_uri' => self::$baseUrl
        ]);
        $response = $client->request('GET', '/api/balances/8b05c363-79a2-4a12-bc5f-e2104852cb54');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);


        //->assertJsonContains([
        //    'ledgerId' => '8b05c363-79a2-4a12-bc5f-e2104852cb54',
        //    'balance' => '111.00'
        //]);

    }

    public function testGetBalanceNotFound(): void
    {
        $client = static::createClient([], [
            'base_uri' => self::$baseUrl
        ]);

        $client->request('GET', '/api/balances/' . Uuid::v4());

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains(['error' => 'Balance not found']);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testCreateLedger( ): void
    {

        $client = static::createClient([], [
            'base_uri' => self::$baseUrl
        ]);
        // Create Currency
        $currency = new Currency();
        $currency->setCode('USD');
        $currency->setSymbol('USD');
        $currency->setName('USD');
        $currency->setIsActive(true);


        $this->entityManager->persist($currency);
        $this->entityManager->flush();

        // Send request to create ledger
        $client->request('POST', '/api/ledgers', [
            'json' => [
                'amount' => '500.00',
                'currency' => $currency->getId(),
                'firstName' => 'John',
                'lastName' => 'Doe'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'currency' => $currency->getCode(),
            'name' => 'John Doe',
            'amount' => '500.00'
        ]);
    }
}
