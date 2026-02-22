<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\Symfony;

use Bl\Qa\Application\Output;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;

/**
 * @object-type ServiceLocator
 */
final readonly class ActionRunner
{
    public function __construct(
        #[AutowireLocator('app.action_handler')]
        private ContainerInterface $handlers,
    ) {
    }

    public function run(object $input): Output
    {
        return $this->handlers->get($input::class.'Handler')->run($input); // @phpstan-ignore method.nonObject, return.type
    }
}
