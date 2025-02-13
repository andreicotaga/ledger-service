<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Ledger;
use Doctrine\ORM\EntityManagerInterface;

class TransactionApiTest extends ApiTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testCreateTransaction()
    {
        $client = self::createClient();

        // Create a Ledger first
        $ledger = new Ledger('Business Account', 'USD');
        $this->entityManager->persist($ledger);
        $this->entityManager->flush();

        // Make API call to create a transaction
        $client->request('POST', '/api/transactions', [
            'json' => [
                'ledger' => "/api/ledgers/" . $ledger->getId(),
                'amount' => '150',
                'currency' => 'USD',
                'transactionType' => 'credit',
                'transactionReference' => 'TXN123450',
            ]
        ]);

        // Validate response
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['amount' => 150, 'currency' => 'USD', 'transactionType' => 'credit']);
    }

    public function testTransactionRateLimiting()
    {
        $client = self::createClient();
        $ledger = new Ledger('Test Ledger', 'USD');
        $this->entityManager->persist($ledger);
        $this->entityManager->flush();

        // Simulate rate limit (exceeding 1000 transactions)
        for ($i = 0; $i < 1001; $i++) {
            $client->request('POST', '/api/transactions', [
                'json' => [
                    'ledger' => "/api/ledgers/" . $ledger->getId(),
                    'amount' => '100',
                    'currency' => 'USD',
                    'transactionType' => 'credit',
                    'transactionReference' => 'TXN123450',
                ]
            ]);
        }

        // Expecting rate limit error on the 1001st request
        $this->assertResponseStatusCodeSame(429);
    }
}
