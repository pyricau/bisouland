# How to manage packages

This monorepo contains both apps and shared packages:

```
bisouland/
├── apps/
│   ├── monolith/   # eXtreme Legacy (2005 LAMP) website
│   ├── qa/         # QA tooling (tests, static analysis, etc)
│   └── ...
└── packages/
    ├── bl-exception/         # Framework-agnostic exception library
    ├── bl-exception-bundle/  # Symfony integration for bl/exception
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
```

**`composer.json`** (plain library):

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

For a Symfony bundle, use `"type": "symfony-bundle"`. To depend on another local package
(e.g. the library it integrates), reference it in `require` — the `repositories` path
entry `"..//*"` resolves sibling packages:

```json
{
    "name": "bl/<name>-bundle",
    "type": "symfony-bundle",
    "description": "BisouLand - <Name> Bundle",
    "license": "Apache-2.0",
    "authors": [
        {"name": "Loïc Faugeron", "email": "faugeron.loic@gmail.com"}
    ],
    "require": {
        "php": ">=8.5",
        "bl/<name>": "*",
        "symfony/framework-bundle": "^8.0"
    },
    "autoload": {
        "psr-4": {
            "Bl\\<Name>Bundle\\": "src/"
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
