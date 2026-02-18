# BisouLand - Game Bundle

Integrates [`bl/game`](../bl-game/README.md) into Symfony by registering the
[`bl/game-pdopg`](../bl-game-pdopg/README.md) implementations and wiring them
to their domain interfaces via autowiring aliases.

## What it does

Registers and aliases all four PDO PostgreSQL implementations:

- `PdoPgSaveNewPlayer` → `SaveNewPlayer`
- `PdoPgFindPlayer` → `FindPlayer`
- `PdoPgApplyCompletedUpgrade` → `ApplyCompletedUpgrade`
- `PdoPgSearchUsernames` → `SearchUsernames`

## Installation

Add to your app's `composer.json`:

```json
"bl/game-bundle": "*@dev"
```

Then update:

```console
make composer arg='update bl/game-bundle'
```

Register the bundle in `config/bundles.php`:

```php
use Bl\GameBundle\BlGameBundle;

return [
    BlGameBundle::class => ['all' => true],
    // ...
];
```
