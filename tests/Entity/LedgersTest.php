<?php
namespace App\Tests\Entity;

use App\Entity\Ledgers;
use App\Entity\Currency;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class LedgersTest extends TestCase
{
    public function testLedgerInitialization(): void
    {
        $ledger = new Ledgers();

        // Check UUID is auto-generated
        $this->assertInstanceOf(Uuid::class, $ledger->getUuid());

        // Check createdAt is set
        $this->assertInstanceOf(\DateTimeImmutable::class, $ledger->getCreatedAt());

        // Check transactions collection is initialized
        $this->assertCount(0, $ledger->getTransactions());
    }

    public function testLedgerValidations(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $ledger = new Ledgers();

        // Set invalid amount
        $ledger->setAmount('-10.50');

        // Set valid currency
        $currency = new Currency();
        $ledger->setCurrency($currency);

        // Validate
        $errors = $validator->validate($ledger);

        $this->assertGreaterThan(0, count($errors), 'Validation should fail for negative amount');
    }

    public function testLedgerBalanceUpdate(): void
    {
        $ledger = new Ledgers();
        $ledger->setAmount('100.00');

        // Apply debit
        $ledger->updateBalance('debit', 30.00);
        $this->assertEquals('70.00', $ledger->getAmount());

        // Apply credit
        $ledger->updateBalance('credit', 50.00);
        $this->assertEquals('120.00', $ledger->getAmount());
    }
}
