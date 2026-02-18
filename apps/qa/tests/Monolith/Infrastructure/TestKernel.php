<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Monolith\Infrastructure;

use Bl\Qa\Infrastructure\Symfony\ActionRunner;
use Bl\Qa\Infrastructure\Symfony\AppKernel;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TestKernel
{
    public static function make(): self
    {
        $appKernel = new AppKernel('test', false);
        $appKernel->boot();

        $container = $appKernel->getContainer();

        $application = new Application($appKernel);
        $application->setAutoExit(false);

        $stderrApplicationTester = new StderrApplicationTester($application);

        $httpClient = $container->get(HttpClientInterface::class);
        if (!$httpClient instanceof HttpClientInterface) {
            throw new \RuntimeException('HttpClientInterface service not found');
        }

        $pdo = $container->get(\PDO::class);
        if (!$pdo instanceof \PDO) {
            throw new \RuntimeException('PDO service not found');
        }

        $actionRunner = $container->get(ActionRunner::class);
        if (!$actionRunner instanceof ActionRunner) {
            throw new \RuntimeException('ActionRunner service not found');
        }

        return new self(
            $appKernel,
            $stderrApplicationTester,
            $container,
            $httpClient,
            $pdo,
            $actionRunner,
        );
    }

    public function __construct(
        private AppKernel $appKernel,
        private ApplicationTester $applicationTester,
        private ContainerInterface $container,
        private HttpClientInterface $httpClient,
        private \PDO $pdo,
        private ActionRunner $actionRunner,
    ) {
    }

    public function appKernel(): AppKernel
    {
        return $this->appKernel;
    }

    public function application(): ApplicationTester
    {
        return $this->applicationTester;
    }

    public function container(): ContainerInterface
    {
        return $this->container;
    }

    public function httpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    public function pdo(): \PDO
    {
        return $this->pdo;
    }

    public function actionRunner(): ActionRunner
    {
        return $this->actionRunner;
    }
}
