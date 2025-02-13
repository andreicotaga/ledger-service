<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;

class LedgerBalanceApiTest extends ApiTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testCreateAndUpdateBalance()
    {
        $client = self::createClient();

        // ✅ Step 1: Create a Ledger
        $ledgerResponse = $client->request('POST', '/api/ledgers', [
            'json' => ['name' => 'Savings Account', 'baseCurrency' => 'USD']
        ]);

        $this->assertResponseIsSuccessful();
        $ledgerData = $ledgerResponse->toArray();
        $this->assertArrayHasKey('id', $ledgerData);
        $ledgerId = $ledgerData['id'];

        // ✅ Step 2: Fetch the Ledger resource URL
        $ledgerIri = "/api/ledgers/$ledgerId";

        // ✅ Step 3: Create a LedgerBalance with a valid Ledger reference
        $balanceResponse = $client->request('POST', '/api/ledger_balances', [
            'json' => [
                'ledger' => $ledgerIri,  // ✅ Correct Ledger reference
                'currency' => 'USD',
                'balance' => "500.00"
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $balanceData = $balanceResponse->toArray();

        $this->assertArrayHasKey('id', $balanceData);
        $this->assertEquals("500.00", $balanceData['balance']);
    }
}
