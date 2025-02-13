<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Ledger;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Cache\ItemInterface;

class LedgerDataPersister implements ProcessorInterface
{
    private EntityManagerInterface $entityManager;
    private CacheItemPoolInterface $cache;

    public function __construct(EntityManagerInterface $entityManager, CacheItemPoolInterface $cache)
    {
        $this->entityManager = $entityManager;
        $this->cache = $cache;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof Ledger) {
            return $data;
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        $this->cache->get('ledger_' . $data->getId(), function (ItemInterface $item) use ($data) {
            $item->expiresAfter(3600);
            return $data;
        });

        return $data;
    }
}
