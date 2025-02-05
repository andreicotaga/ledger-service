<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class Kernel extends BaseKernel
{
  use MicroKernelTrait;

  public function boot(): void
  {
    parent::boot();

    $limiter = $this->container->get(RateLimiterFactory::class)->create('api');
    if (!$limiter->consume(1)->isAccepted()) {
      throw new TooManyRequestsHttpException();
    }
  }
}
