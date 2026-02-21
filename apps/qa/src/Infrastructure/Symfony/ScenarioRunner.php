<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\Symfony;

use Bl\Qa\Application\Scenario\ScenarioOutput;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;

/**
 * @object-type ServiceLocator
 */
final readonly class ScenarioRunner
{
    public function __construct(
        #[AutowireLocator('app.scenario_handler')]
        private ContainerInterface $handlers,
    ) {
    }

    public function run(object $input): ScenarioOutput
    {
        return $this->handlers->get($input::class.'Handler')->run($input); // @phpstan-ignore method.nonObject, return.type
    }
}
