<?php

namespace App\Service;

use Predis\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class TransactionsRateLimiter
{
    private Client $redis;
    private int $limit;
    private int $timeWindow;

    public function __construct(Client $redis, int $limit = 1000, int $timeWindow = 60)
    {
        $this->redis = $redis;
        $this->limit = $limit;
        $this->timeWindow = $timeWindow; // 60 seconds (1 min)
    }

    public function consume(Request $request): void
    {
        $ip = $request->getClientIp() ?? 'unknown';
        $key = "rate_limit:transactions:$ip";

        // Get current request count
        $currentCount = $this->redis->get($key) ?? 0;

        if ($currentCount >= $this->limit) {
            throw new TooManyRequestsHttpException($this->timeWindow, 'Too many transactions, try again later.');
        }

        // Increment request count
        $this->redis->incr($key);

        // Set expiration if first request
        if ($currentCount == 0) {
            $this->redis->expire($key, $this->timeWindow);
        }
    }
}
