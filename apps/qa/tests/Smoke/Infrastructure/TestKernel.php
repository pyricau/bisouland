<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Smoke\Infrastructure;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class TestKernel
{
    public static function make(): self
    {
        $httpClient = HttpClient::createForBaseUri('http://web/');

        return new self(
            $httpClient,
        );
    }

    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    public function httpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }
}
