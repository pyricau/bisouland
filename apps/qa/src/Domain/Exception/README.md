# Domain Exceptions

```
AppException (500 INTERNAL SERVER ERROR)
├── ClientErrorException (400 BAD REQUEST)
├── UnauthorizedException (401 UNAUTHORIZED)
├── ForbiddenException (403 FORBIDDEN)
├── NotFoundException (404 NOT FOUND)
├── ValidationFailedException (422 UNPROCESSABLE ENTITY)
└── ServerErrorException (500 INTERNAL SERVER ERROR)
```

All exceptions extend `AppException`, which extends PHP's `\DomainException`.

## Usage

Use the `make()` factory method to create exceptions:

```php
throw ValidationFailedException::make(
    'Invalid "AccountId" parameter: it should be a valid UUID (`not-a-uuid` given)',
);
```

Chain a previous exception when wrapping lower-level errors:

```php
throw ServerErrorException::make(
    'Failed to SaveNewPlayer (`BisouLand`): unexpected database error',
    $pdoException,
);
```

## Message conventions

Validation messages follow:

```
Invalid "ClassName" parameter: reason (`value` given)
```

Server error messages follow:

```
Failed to ServiceName (`context`): reason
```
