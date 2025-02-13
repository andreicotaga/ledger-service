<?php

namespace App\Message;

use Symfony\Component\Uid\Uuid;

class TransactionMessage
{
    public function __construct(
        private readonly Uuid   $ledgerId,
        private readonly float  $amount,
        private readonly string $currency,
        private readonly string $transactionType
    )
    {
    }

    public function getLedgerId(): Uuid
    {
        return $this->ledgerId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getTransactionType(): string
    {
        return $this->transactionType;
    }
}
