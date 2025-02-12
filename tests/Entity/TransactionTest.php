<?php
namespace App\Tests\Entity;

use App\Entity\Transaction;
use App\Entity\Ledgers;
use App\Entity\Currency;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class TransactionTest extends TestCase
{
    public function testTransactionInitialization(): void
    {
        $transaction = new Transaction();

        // Ensure createdAt is automatically set
        $this->assertInstanceOf(\DateTimeImmutable::class, $transaction->getCreatedAt());

        // Ensure properties are null by default
        $this->assertNull($transaction->getLedger());
        $this->assertNull($transaction->getType());
        $this->assertNull($transaction->getAmount());
        $this->assertNull($transaction->getCurrency());
        $this->assertNull($transaction->getTransactionId());
    }

    public function testTransactionSettersAndGetters(): void
    {
        $transaction = new Transaction();
        $ledger = new Ledgers();
        $currency = new Currency();

        $transaction->setLedger($ledger);
        $transaction->setType('debit');
        $transaction->setAmount('500.00');
        $transaction->setCurrency($currency);
        $transaction->setTransactionId('txn_12345');

        $this->assertSame($ledger, $transaction->getLedger());
        $this->assertEquals('debit', $transaction->getType());
        $this->assertEquals('500.00', $transaction->getAmount());
        $this->assertSame($currency, $transaction->getCurrency());
        $this->assertEquals('txn_12345', $transaction->getTransactionId());
    }

    public function testTransactionValidation(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $transaction = new Transaction();

        // Invalid type (should be 'debit' or 'credit')
        $transaction->setType('invalidType');
        $transaction->setAmount('-10.50'); // Negative amount

        $errors = $validator->validate($transaction);
        $this->assertGreaterThan(0, count($errors), 'Validation should fail for invalid type or negative amount');
    }
}
