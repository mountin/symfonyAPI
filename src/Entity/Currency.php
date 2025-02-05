<?php

namespace App\Entity;

use App\Repository\CurrencyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
class Currency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 3)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 10)]
    private ?string $symbol = null;

    #[ORM\Column]
    private ?bool $is_active = null;

    /**
     * @var Collection<int, Ledgers>
     */
    #[ORM\OneToMany(targetEntity: Ledgers::class, mappedBy: 'currency')]
    private Collection $ledgers;

    public function __construct()
    {
        $this->ledgers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): static
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): static
    {
        $this->is_active = $is_active;

        return $this;
    }

    /**
     * @return Collection<int, Ledgers>
     */
    public function getLedgers(): Collection
    {
        return $this->ledgers;
    }

    public function addLedger(Ledgers $ledger): static
    {
        if (!$this->ledgers->contains($ledger)) {
            $this->ledgers->add($ledger);
            $ledger->setCurrency($this);
        }

        return $this;
    }

    public function removeLedger(Ledgers $ledger): static
    {
        if ($this->ledgers->removeElement($ledger)) {
            // set the owning side to null (unless already changed)
            if ($ledger->getCurrency() === $this) {
                $ledger->setCurrency(null);
            }
        }

        return $this;
    }

    public function __toString(){
        return '';
    }

}
