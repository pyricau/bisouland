# BisouLand - Exception Bundle

Integrates [`bl/exception`](../bl-exception/README.md) into Symfony by providing an event listener that converts exceptions into JSON API responses.

## What it does

Registers `AppExceptionListener`, which listens to `ExceptionEvent` for requests under `/api` and converts exceptions to JSON responses:

- `AppException`: uses its message and HTTP status code
- `HttpException`: uses its message and HTTP status code
- Any other exception: logs it with a UUID token, returns 500 with a token for the user to report

Unexpected exceptions are logged at `error` level with full context (`exception` and `token`).

## Installation

Add to your app's `composer.json`:

```json
"bl/exception-bundle": "*@dev"
```

Then update:

```console
make composer arg='update bl/exception-bundle'
```

Register the bundle in `config/bundles.php`:

```php
use Bl\ExceptionBundle\BlExceptionBundle;

return [
    BlExceptionBundle::class => ['all' => true],
    // ...
];
```
