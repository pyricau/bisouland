# How to manage packages

This monorepo contains both apps and shared packages:

```
bisouland/
├── apps/
│   ├── monolith/   # eXtreme Legacy (2005 LAMP) website
│   ├── qa/         # QA tooling (tests, static analysis, etc)
│   └── ...
└── packages/
    ├── bl-auth/              # Auth domain (Account, AuthToken, etc.)
    ├── bl-auth-pdopg/        # PDO PostgreSQL implementations for bl/auth
    ├── bl-auth-tests/        # Test fixtures for bl/auth
    ├── bl-exception/         # Framework-agnostic exception library
    ├── bl-exception-bundle/  # Symfony integration for bl/exception
    ├── bl-game/              # Game domain (Player, upgradables, etc.)
    ├── bl-game-bundle/       # Symfony integration for bl/game
    ├── bl-game-pdopg/        # PDO PostgreSQL implementations for bl/game
    ├── bl-game-tests/        # Test fixtures for bl/game
    └── ...
```

Apps live in `apps/`, reusable libraries in `packages/`.

## How packages are made available to apps

Each app's `compose.yaml` bind-mounts the packages directory into Docker:

```yaml
# apps/qa/compose.yaml
volumes:
  - ../../packages:/packages
```

Each app's `composer.json` declares a path repository pointing to that mount:

```json
// apps/qa/composer.json
"repositories": [
    {"type": "path", "url": "../../packages/*"}
]
```

Composer resolves the package name (e.g. `bl/exception`, etc) from the local filesystem
instead of Packagist.

## How to create a new package

Create a directory under `packages/`:

```
packages/bl-<name>/
├── .gitignore
├── README.md
├── composer.json
└── src/
```

**`.gitignore`:**

```
# composer
composer.lock
vendor/

# editors
*.swp
```

**`composer.json`** (plain library, no sibling dependencies, e.g. `bl/exception`):

```json
{
    "name": "bl/<name>",
    "type": "library",
    "description": "BisouLand - <Name>",
    "license": "Apache-2.0",
    "authors": [
        {"name": "Loïc Faugeron", "email": "faugeron.loic@gmail.com"}
    ],
    "require": {
        "php": ">=8.5"
    },
    "autoload": {
        "psr-4": {
            "Bl\\<Name>\\": "src/"
        }
    },
    "repositories": [
        {"type": "path", "url": "../*"}
    ]
}
```

When a package depends on sibling packages, reference them with `"*@dev"` in `require`,
composer resolves them from the `"../*"` path repository (e.g. `bl/auth` depends on `bl/exception`):

```json
{
    "name": "bl/auth",
    "type": "library",
    "description": "BisouLand - Auth",
    "license": "Apache-2.0",
    "authors": [
        {"name": "Loïc Faugeron", "email": "faugeron.loic@gmail.com"}
    ],
    "require": {
        "php": ">=8.5",
        "bl/exception": "*@dev",
        "symfony/uid": "^8.0"
    },
    "autoload": {
        "psr-4": {
            "Bl\\Auth\\": "src/"
        }
    },
    "repositories": [
        {"type": "path", "url": "../*"}
    ]
}
```

Infrastructure packages follow the same pattern but scope the namespace further
(e.g. `bl/auth-pdopg` implements `bl/auth` service interfaces using PDO PostgreSQL):

```json
{
    "name": "bl/auth-pdopg",
    "type": "library",
    "description": "BisouLand - Auth PDO PostgreSQL",
    "license": "Apache-2.0",
    "authors": [
        {"name": "Loïc Faugeron", "email": "faugeron.loic@gmail.com"}
    ],
    "require": {
        "php": ">=8.5",
        "ext-pdo_pgsql": "*",
        "bl/auth": "*@dev",
        "bl/exception": "*@dev"
    },
    "autoload": {
        "psr-4": {
            "Bl\\Auth\\PdoPg\\": "src/"
        }
    },
    "repositories": [
        {"type": "path", "url": "../*"}
    ]
}
```

Test fixture packages mirror the library namespace with a `Tests` suffix
and keep `phpunit` in `require-dev` so the package can be developed standalone
(e.g. `bl/auth-tests` ships ready-made fixtures for `bl/auth`):

```json
{
    "name": "bl/auth-tests",
    "type": "library",
    "description": "BisouLand - Auth Test Fixtures",
    "license": "Apache-2.0",
    "authors": [
        {"name": "Loïc Faugeron", "email": "faugeron.loic@gmail.com"}
    ],
    "require": {
        "php": ">=8.5",
        "bl/auth": "*@dev"
    },
    "require-dev": {
        "phpspec/prophecy-phpunit": "^2.4",
        "phpunit/phpunit": "^12.0"
    },
    "autoload": {
        "psr-4": {
            "Bl\\Auth\\Tests\\": "src/"
        }
    },
    "repositories": [
        {"type": "path", "url": "../*"}
    ]
}
```

For a Symfony bundle, use `"type": "symfony-bundle"` and depend on the library it integrates
(e.g. `bl/exception-bundle` integrates `bl/exception` into Symfony):

```json
{
    "name": "bl/exception-bundle",
    "type": "symfony-bundle",
    "description": "BisouLand - Exception Bundle",
    "license": "Apache-2.0",
    "authors": [
        {"name": "Loïc Faugeron", "email": "faugeron.loic@gmail.com"}
    ],
    "require": {
        "php": ">=8.5",
        "bl/exception": "*@dev",
        "symfony/framework-bundle": "^8.0"
    },
    "autoload": {
        "psr-4": {
            "Bl\\ExceptionBundle\\": "src/"
        }
    },
    "repositories": [
        {"type": "path", "url": "../*"}
    ]
}
```

The root class extends `AbstractBundle` and lives at `src/Bl<Name>Bundle.php`.

## How to install a package in an app

1. **Add it to the app's `composer.json`** under `require`:

```json
"<name>": "*@dev"
```

2. **Recreate the Docker container** if the package directory is new
   (so Docker picks up the bind mount):

```console
cd apps/qa
make app-init # docker compose down && docker compose up -d
```

3. **Update the lock file**:

```console
make composer arg='update <name>'
```

For a Symfony bundle, also register it in `config/bundles.php`:

```php
use Bl\<Name>Bundle\Bl<Name>Bundle;

return [
    Bl<Name>Bundle::class => ['all' => true],
    // ...
];
```

## Testing and quality tools

Packages have no standalone test runner or quality tooling. Everything is centralised
in `apps/qa`. See [003-how-to-run-qa.md](003-how-to-run-qa.md).
