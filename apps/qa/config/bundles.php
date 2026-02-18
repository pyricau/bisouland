<?php

declare(strict_types=1);
use Bl\ExceptionBundle\BlExceptionBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MakerBundle\MakerBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;

return [
    BlExceptionBundle::class => ['all' => true],
    FrameworkBundle::class => ['all' => true],
    MonologBundle::class => ['all' => true],
    TwigBundle::class => ['all' => true],
    MakerBundle::class => ['dev' => true],
];
