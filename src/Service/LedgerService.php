<?php

namespace App\Service;

use App\Entity\Ledger;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Cache\ItemInterface;

class LedgerService
{
    private EntityManagerInterface $entityManager;
    private CacheItemPoolInterface $cache;

    public function __construct(EntityManagerInterface $entityManager, CacheItemPoolInterface $cache)
    {
        $this->entityManager = $entityManager;
        $this->cache = $cache;
    }

    public function createLedger(string $name, string $baseCurrency): Ledger
    {
        $ledger = new Ledger($name, strtoupper($baseCurrency));
        $this->entityManager->persist($ledger);
        $this->entityManager->flush();

        // Store ledger in cache
        $this->cache->get('ledger_' . $ledger->getId(), function (ItemInterface $item) use ($ledger) {
            $item->expiresAfter(3600);
            return $ledger;
        });

        return $ledger;
    }
}
