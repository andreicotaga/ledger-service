<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class LedgerApiTest extends ApiTestCase
{
    public function testCreateLedger()
    {
        $client = self::createClient();

        $client->request('POST', '/api/ledgers', [
            'json' => [
                'name' => 'Personal Ledger',
                'baseCurrency' => 'EUR'
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['name' => 'Personal Ledger', 'baseCurrency' => 'EUR']);
    }

    public function testGetLedgers()
    {
        $client = self::createClient();

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
