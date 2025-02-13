<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Transaction;
use App\Entity\LedgerBalance;
use Doctrine\ORM\EntityManagerInterface;

class TransactionDataPersister implements ProcessorInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if (!$data instanceof Transaction) {
            return;
        }

        $ledger = $data->getLedger();
        $ledgerBalanceRepo = $this->entityManager->getRepository(LedgerBalance::class);

        $ledgerBalance = $ledgerBalanceRepo->findOneBy([
            'ledger' => $ledger,
            'currency' => $data->getCurrency(),
        ]);

        if (!$ledgerBalance) {
            $ledgerBalance = new LedgerBalance();
        }

        $balanceChange = ($data->getTransactionType() === 'credit') ? $data->getAmount() : -$data->getAmount();
        $ledgerBalance->setBalance($ledgerBalance->getBalance() + $balanceChange);

        $this->entityManager->persist($data);
        $this->entityManager->persist($ledgerBalance);
        $this->entityManager->flush();
    }
}
