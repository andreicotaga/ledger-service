<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\LedgerDataPersister;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(processor: LedgerDataPersister::class),
    ]
)]
#[ApiResource]
#[ORM\Entity]
class Ledger
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'string', length: 3)]
    #[Assert\Length(min: 3, max: 3)]
    private string $baseCurrency;

    public function __construct(string $name, string $baseCurrency = 'USD')
    {
        $this->name = $name;
        $this->baseCurrency = strtoupper($baseCurrency);
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    public function setBaseCurrency(string $baseCurrency): void
    {
        $this->baseCurrency = $baseCurrency;
    }
}
