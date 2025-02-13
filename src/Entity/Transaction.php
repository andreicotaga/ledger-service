<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\State\TransactionProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(processor: TransactionProcessor::class),
        new GetCollection(uriTemplate: '/ledgers/{id}/transactions', provider: TransactionProcessor::class),
    ],
    normalizationContext: ['groups' => ['transaction:read']],
    denormalizationContext: ['groups' => ['transaction:write']]
)]
#[ORM\Entity]
class Transaction
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Assert\Uuid]
    #[Groups(['transaction:read'])]
    private ?Uuid $id;

    #[ORM\ManyToOne(targetEntity: Ledger::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['transaction:read', 'transaction:write'])]
    private ?Ledger $ledger = null;

    #[ORM\Column(type: 'string', length: 10)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['debit', 'credit'], message: 'Transaction type must be debit or credit.')]
    #[Groups(['transaction:read', 'transaction:write'])]
    private ?string $transactionType = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 6)]
    #[Assert\PositiveOrZero]
    #[Groups(['transaction:read', 'transaction:write'])]
    private string $amount;  // **Changed to string**

    #[ORM\Column(type: 'string', length: 3)]
    #[Assert\Length(min: 3, max: 3)]
    #[Assert\NotBlank]
    #[Groups(['transaction:read', 'transaction:write'])]
    private ?string $currency = null;

    #[ORM\Column(type: 'string', unique: true)]
    #[Assert\NotBlank]
    #[Groups(['transaction:read', 'transaction:write'])]
    private ?string $transactionReference = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getLedger(): ?Ledger
    {
        return $this->ledger;
    }

    public function setLedger(Ledger $ledger): void
    {
        $this->ledger = $ledger;
    }

    public function getTransactionType(): ?string
    {
        return $this->transactionType;
    }

    public function setTransactionType(string $transactionType): void
    {
        $this->transactionType = $transactionType;
    }

    public function getAmount(): float
    {
        return (float)$this->amount; // **Convert to float when accessing**
    }

    public function setAmount(float $amount): void
    {
        $this->amount = (string)$amount; // **Store as string**
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = strtoupper($currency);
    }

    public function getTransactionReference(): ?string
    {
        return $this->transactionReference;
    }

    public function setTransactionReference(string $transactionReference): void
    {
        $this->transactionReference = $transactionReference;
    }
}
