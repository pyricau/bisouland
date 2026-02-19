# How to run QA

All quality tooling (tests, static analysis, coding standards, automated refactoring)
is centralised in `apps/qa`.

It covers all `apps` and `packages/`.

## Run the full pipeline

From the monorepo root you can run:

```console
make apps-qa
```

But all it does is execute the `make app-qa` from apps/qa:

```console
cd apps/qa
make app-qa
```

This runs in order: `cs-check`, `phpstan-analyze`, `rector-check`, `phpunit`.
It'll take care of composer autoloading (`composer dump -o`),
as well as the Symfony cache (`bin/sf-cc-if-stale.sh`).

## Run individual tools

```console
cd apps/qa

make phpunit              # all test suites
make phpstan-analyze      # static analysis
make cs-check             # coding standards check (PHP-CS-Fixer)
make cs-fix               # fix coding standards violations
make rector-check         # automated refactoring check (Rector)
make rector-fix           # apply automated refactorings
make composer-dump        # PSR-4 compliance check (FQCN, classnames, filenames, namespaces)
```

`composer-dump` runs with `--strict-psr` and `--strict-ambiguous`, which validates that
FQCNs, class names, file names, and file paths all follow PSR-4. Violations are reported
as autoload errors. `cs-fix` uses [Swiss Knife](https://github.com/rectorphp/swiss-knife)
to automatically align namespaces and file paths before running PHP-CS-Fixer.

To pass `arg` to run specific arguments:

```console
make phpunit arg='--testsuite packages-spec,monolith-smoke'
make phpunit arg='--testdox --filter Auth'
```

## How packages are covered

Packages have no standalone test runner or quality tooling.

Coverage is wired into `apps/qa` configuration:

**`apps/qa/phpunit.xml.dist`**, discovers package tests via the `packages-spec` suite:

```xml
<testsuite name="packages-spec">
    <directory>../../packages/*/tests/Spec</directory>
</testsuite>
```

The `source` section also includes packages so coverage is tracked:

```xml
<source>
    <include>
        <directory>../../packages</directory>
    </include>
</source>
```

**`apps/qa/phpstan.neon.dist`**, includes packages in static analysis paths:

```neon
parameters:
  paths:
    - ../../packages/
```

**PHP-CS-Fixer and Rector** configs in `apps/qa` cover packages via their path
configuration.

## Package test fixtures

If a package has tests, its `composer.json` declares `autoload-dev` so fixtures are
loadable:

```json
"autoload-dev": {
    "psr-4": {
        "Bl\\<Name>\\Tests\\": "tests/"
    }
}
```

When app tests need to use those fixtures (e.g. a `PlayerFixture` referencing an
`AccountFixture` from another package), mirror the entry in `apps/qa/composer.json`:

```json
"autoload-dev": {
    "psr-4": {
        "Bl\\<Name>\\Tests\\": "../../packages/bl-<name>/tests/"
    }
}
```
