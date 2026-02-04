<?php

declare(strict_types=1);

use Bl\Qa\Infrastructure\Symfony\AppKernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return static fn (array $context): AppKernel => new AppKernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
