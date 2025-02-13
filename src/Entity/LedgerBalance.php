<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['ledger_balance:read']]),
        new GetCollection(normalizationContext: ['groups' => ['ledger_balance:read']]),
        new Post(denormalizationContext: ['groups' => ['ledger_balance:write']]),
        new Put(denormalizationContext: ['groups' => ['ledger_balance:write']]),
        new Delete()
    ]
)]
#[ORM\Entity]
#[ORM\Table(name: "ledger_balances")]
class LedgerBalance
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['ledger_balance:read'])]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: Ledger::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(['ledger_balance:read', 'ledger_balance:write'])]
    private ?Ledger $ledger = null; // ✅ Allow NULL for deserialization

    #[ORM\Column(type: 'string', length: 3)]
    #[Groups(['ledger_balance:read', 'ledger_balance:write'])]
    private string $currency = 'USD';

    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    #[Groups(['ledger_balance:read', 'ledger_balance:write'])]
    private float $balance = 0.0;

    public function __construct() {}

    public static function create(Ledger $ledger, string $currency, float $balance): self
    {
        $instance = new self();
        $instance->id = Uuid::v4();
        $instance->ledger = $ledger;
        $instance->currency = $currency;
        $instance->balance = $balance;

        return $instance;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getLedger(): ?Ledger
    {
        return $this->ledger;
    }

    public function setLedger(Ledger $ledger): self
    {
        $this->ledger = $ledger;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;
        return $this;
    }
}
