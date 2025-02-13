<?php

namespace App\Tests\Entity;

use App\Entity\Ledger;
use PHPUnit\Framework\TestCase;

class LedgerTest extends TestCase
{
    public function testLedgerInitialization()
    {
        $ledger = new Ledger('Business Account', 'EUR');

        $this->assertEquals('Business Account', $ledger->getName());
        $this->assertEquals('EUR', $ledger->getBaseCurrency());
    }
}
