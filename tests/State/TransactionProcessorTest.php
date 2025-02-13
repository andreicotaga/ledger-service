<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Operation;
use App\Entity\Ledger;
use App\Entity\Transaction;
use App\Message\TransactionMessage;
use App\State\TransactionProcessor;
use App\Service\TransactionsRateLimiter;
use PHPUnit\Framework\TestCase;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Cache\CacheInterface;

class TransactionProcessorTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testTransactionIsQueued()
    {
        $bus = $this->createMock(MessageBusInterface::class);
        $cache = $this->createMock(CacheInterface::class);
        $operation = $this->createMock(Operation::class);
        $rateLimiter = $this->createMock(TransactionsRateLimiter::class);
        $requestStack = $this->createMock(RequestStack::class);
        $request = new Request();

        $rateLimiter->expects($this->once()) // ✅ Expect rate limiter call
        ->method('consume')
            ->with($this->equalTo($request));

        $requestStack->method('getCurrentRequest')->willReturn($request);

        $ledger = new Ledger('Test Ledger', 'USD');

        $reflection = new \ReflectionClass(Ledger::class);
        $property = $reflection->getProperty('id');
        $property->setValue($ledger, Uuid::v4());

        $transaction = new Transaction();
        $transaction->setLedger($ledger);
        $transaction->setAmount(100);
        $transaction->setCurrency('USD');
        $transaction->setTransactionType('credit');

        $bus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(TransactionMessage::class))
            ->willReturn(new Envelope(new TransactionMessage(Uuid::v4(), 100, 'USD', 'credit')));

        $processor = new TransactionProcessor($bus, $cache, $rateLimiter, $requestStack);
        $result = $processor->process($transaction, $operation);

        $this->assertInstanceOf(Transaction::class, $result);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testInvalidTransactionReturnsEmptyArray()
    {
        $bus = $this->createMock(MessageBusInterface::class);
        $cache = $this->createMock(CacheInterface::class);
        $operation = $this->createMock(Operation::class);
        $rateLimiter = $this->createMock(TransactionsRateLimiter::class); // ✅ Updated
        $requestStack = $this->createMock(RequestStack::class);
        $request = new Request();

        $requestStack->method('getCurrentRequest')->willReturn($request);

        $rateLimiter->expects($this->once())
            ->method('consume')
            ->with($this->equalTo($request));

        $processor = new TransactionProcessor($bus, $cache, $rateLimiter, $requestStack);
        $result = $processor->process(null, $operation);

        $this->assertEquals([], $result);
    }
}
