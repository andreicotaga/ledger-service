<?php

namespace App\Tests\Entity;

use App\Entity\Ledger;
use App\Entity\LedgerBalance;
use PHPUnit\Framework\TestCase;

class LedgerBalanceTest extends TestCase
{
    public function testBalanceOperations()
    {
        $ledger = new Ledger('Savings', 'USD');
        $balance = new LedgerBalance();
        $balance->setLedger($ledger);
        $balance->setBalance(500);
        $balance->setCurrency('USD');

        $this->assertEquals(500, $balance->getBalance());

        $balance->setBalance(1000);
        $this->assertEquals(1000, $balance->getBalance());
    }
}
