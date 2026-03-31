<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\Twig;

use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class ActionsExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * @return array{actions: list<array{name: string, path: string}>, scenarios: list<array{name: string, path: string}>}
     */
    public function getGlobals(): array
    {
        $actions = [];
        $scenarios = [];
        foreach ($this->router->getRouteCollection()->all() as $route) {
            $path = $route->getPath();
            if (str_starts_with($path, '/actions/') && \in_array('GET', $route->getMethods())) {
                $kebab = substr($path, \strlen('/actions/'));
                $actions[] = ['name' => ucwords(str_replace('-', ' ', $kebab)), 'path' => $path];
            }

            if (str_starts_with($path, '/scenarios/') && \in_array('GET', $route->getMethods())) {
                $kebab = substr($path, \strlen('/scenarios/'));
                $scenarios[] = ['name' => ucwords(str_replace('-', ' ', $kebab)), 'path' => $path];
            }
        }

        return ['actions' => $actions, 'scenarios' => $scenarios];
    }
}
