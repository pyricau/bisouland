<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

try {
    require __DIR__.'/../phpincludes/app.php';
} catch (Throwable $throwable) {
    http_response_code(500);
    error_log($throwable->getMessage());
    echo 'An error occurred';
}
