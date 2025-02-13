<?php

namespace App\MessageHandler;

use App\Message\TransactionMessage;
use App\Entity\LedgerBalance;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TransactionMessageHandler
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(TransactionMessage $message)
    {
        $this->entityManager->beginTransaction();

        try {
            $ledger = $this->entityManager->getRepository(LedgerBalance::class)
                ->findOneBy(['ledger' => $message->getLedgerId(), 'currency' => $message->getCurrency()]);

            if (!$ledger) {
                $ledger = new LedgerBalance();
                $this->entityManager->persist($ledger);
            }

            if ($message->getTransactionType() === 'credit') {
                $ledger->setBalance($ledger->getBalance() + $message->getAmount());
            } elseif ($message->getTransactionType() === 'debit') {
                if ($ledger->getBalance() < $message->getAmount()) {
                    throw new \Exception("Insufficient funds.");
                }
                $ledger->setBalance($ledger->getBalance() - $message->getAmount());
            }

            $transaction = new Transaction();
            $transaction->setLedger($ledger);
            $transaction->setAmount($message->getAmount());
            $transaction->setCurrency($message->getCurrency());
            $transaction->setTransactionType($message->getTransactionType());

            $this->entityManager->persist($transaction);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }
}
