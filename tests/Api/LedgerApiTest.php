<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class LedgerApiTest extends ApiTestCase
{
    public function testCreateLedger()
    {
        $client = self::createClient();

        // Make API call to create a Ledger
        $response = $client->request('POST', '/api/ledgers', [
            'json' => [
                'name' => 'Personal Ledger',
                'baseCurrency' => 'EUR'
            ]
        ]);

        // Validate response
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['name' => 'Personal Ledger', 'baseCurrency' => 'EUR']);
    }

    public function testGetLedgers()
    {
        $client = self::createClient();

        // Ensure a Ledger exists before fetching
        $client->request('POST', '/api/ledgers', [
            'json' => [
                'name' => 'Test Ledger',
                'baseCurrency' => 'USD'
            ]
        ]);

        $client->request('GET', '/api/ledgers');

        $this->assertResponseIsSuccessful();
    }
}
