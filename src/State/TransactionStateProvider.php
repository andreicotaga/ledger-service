<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class TransactionStateProvider implements ProviderInterface
{
    private EntityManagerInterface $entityManager;
    private CacheInterface $cache;

    public function __construct(EntityManagerInterface $entityManager, CacheInterface $cache)
    {
        $this->entityManager = $entityManager;
        $this->cache = $cache;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|null|object
    {
        $ledgerId = $uriVariables['id'] ?? null;

        if (!$ledgerId) {
            throw new \InvalidArgumentException('Ledger ID is required.');
        }

        return $this->cache->get("transactions_{$ledgerId}", function () use ($ledgerId) {
            return $this->entityManager->getRepository(Transaction::class)
                ->findBy(['ledger' => $ledgerId]);
        });
    }
}
