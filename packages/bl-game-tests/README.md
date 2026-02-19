# BisouLand - Game Tests

Tests and Fixtures for the `bl/game` package ("library under test").

## Why a separate package?

Any package or app that depends on the "library under test",
and needs to write tests can require this package to get ready-made fixtures,
without coupling their test setup to the library's internals.

The QA app will also use this package to run its tests.

## Fixtures

Fixtures are reusable test data builders for the "library under test" classes.

All fixtures expose `make()` returning a typed object and `make<Primitive>()`
(e.g. `makeString()`, `makeInt()`, `makeX()`, `makeY()`) returning a raw value.
