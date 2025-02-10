<?php

namespace App\Entity;

use App\Controller\LedgersController;
use App\Repository\LedgersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;


#[ORM\Entity(repositoryClass: LedgersRepository::class)]
#[ORM\Table(name: 'ledgers', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'unique_ledger_currency', columns: ['currency_id', 'uuid'])
])]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/balances/{ledgerId}',
            controller: LedgersController::class,
            name:'get_balances'),
        new GetCollection(uriTemplate: '/ledgers'), // Get all ledgers
        new Post(
            uriTemplate: '/ledgers',
            controller: LedgersController::class, // Custom Controller
            deserialize: false,
        )

    ]
)]
#[ApiFilter(OrderFilter::class, properties: ['amount' => 'ASC', 'firstName' => 'DESC'])]
#[ApiFilter(SearchFilter::class, properties: ['uuid' => 'partial', 'currency' => 'exact'])]
class Ledgers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotNull(message: "Amount is required.")]
    #[Assert\Type(
        type: 'numeric',
        message: "Amount should be a numeric."
    )]
    #[Assert\Positive(message: "Amount should be positive")]
    private ?string $amount = null;

    #[ORM\ManyToOne(inversedBy: 'ledgers', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Currency $currency = null;

    #[ORM\Column(type: 'uuid', options: ['index' => true]) ]
    #[Assert\NotNull(message: "uuID required.")]
    #[Assert\Uuid(
        message: 'The value must be a valid UUID'
    )]
    private ?Uuid $uuid = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotNull(message: "Name required.")]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotNull(message: "Last Name required.")]
    private ?string $lastName = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'ledger')]
    private Collection $transactions;

    public function __construct()
    {
        $this->uuid = Uuid::v4(); // Generate a UUID (v4) when the entity is created
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrency(): ?String
    {
        return $this->currency->getCode();
    }

    public function setCurrency(?Currency $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setLedger($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getLedger() === $this) {
                $transaction->setLedger(null);
            }
        }

        return $this;
    }

    public function updateBalance(string $type, float $amount): void
    {
        if ($type === 'debit') {
            $this->amount -= $amount;
        } else {
            $this->amount += $amount;
        }
    }

}
