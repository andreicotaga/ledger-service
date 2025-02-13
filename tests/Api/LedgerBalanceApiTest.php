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

        $ledgerResponse = $client->request('POST', '/api/ledgers', [
            'json' => ['name' => 'Savings Account', 'baseCurrency' => 'USD']
        ]);

        $this->assertResponseIsSuccessful();
        $ledgerData = $ledgerResponse->toArray();
        $this->assertArrayHasKey('id', $ledgerData);
        $ledgerId = $ledgerData['id'];

        $ledgerIri = "/api/ledgers/$ledgerId";

        $balanceResponse = $client->request('POST', '/api/ledger_balances', [
            'json' => [
                'ledger' => $ledgerIri,
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
