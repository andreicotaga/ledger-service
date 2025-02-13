<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Transaction;
use App\Message\TransactionMessage;
use App\Service\TransactionsRateLimiter;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Cache\CacheInterface;

class TransactionProcessor implements ProcessorInterface
{
    private MessageBusInterface $bus;
    private CacheInterface $cache;
    private TransactionsRateLimiter $transactionsLimiter;
    private RequestStack $requestStack;

    public function __construct(
        MessageBusInterface $bus,
        CacheInterface $cache,
        TransactionsRateLimiter $transactionsLimiter,
        RequestStack $requestStack
    ) {
        $this->bus = $bus;
        $this->cache = $cache;
        $this->transactionsLimiter = $transactionsLimiter;
        $this->requestStack = $requestStack;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Transaction|array
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            throw new \RuntimeException('No HTTP request found for rate limiting.');
        }

        $this->transactionsLimiter->consume($request);

        if (!$data instanceof Transaction) {
            return [];
        }

        $this->bus->dispatch(new TransactionMessage(
            $data->getLedger()->getId(),
            $data->getAmount(),
            $data->getCurrency(),
            $data->getTransactionType()
        ));

        $cacheKey = "ledger_balance_{$data->getLedger()->getId()}";
        $this->cache->delete($cacheKey);

        return $data;
    }
}
