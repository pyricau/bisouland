# Switch cost/milli_score to arbitrary precision (bcmath + NUMERIC)

## Problem

`Upgradable::computeCost()` uses `ceil(base * exp(rate * level))` cast to `int`.
The result silently wraps when it exceeds `PHP_INT_MAX` (~9.2e18):

- Leap (base 10000, rate 0.6) at level 58
- Legs (base 1000, rate 0.6) at level 63
- Soup (base 5000, rate 0.4) at level 88
- Spit (base 3000, rate 0.4) at level 89
- Flirt (base 2000, rate 0.4) at level 90
- Heart (base 100, rate 0.4) at level 98

The full chain is `int` end-to-end: PHP `int` -> PDO param -> PostgreSQL `BIGINT` -> PDO fetch -> PHP `int` -> JSON/CLI output. All links break at the same threshold.

## Solution

Replace `int` with `string` (bcmath) in PHP and `BIGINT` with `NUMERIC` in PostgreSQL.

### PostgreSQL schema (`apps/monolith/schema.sql`)

- `membres.score`: `BIGINT` -> `NUMERIC`
- `membres.amour`: `BIGINT` -> `NUMERIC`
- `evolution.cout`: `BIGINT` -> `NUMERIC`
- `liste.cout`: `BIGINT` -> `NUMERIC`
- `attaque.butin`: `BIGINT` -> `NUMERIC`

### PHP domain (`apps/qa/src/`)

- `Upgradable::computeCost()`: return `string` instead of `int`, use `bcmul`/`bcpow` or convert the float result via `number_format($cost, 0, '', '')`
- `MilliScore`: store `string`, expose `fromString()`/`toString()` instead of `fromInt()`/`toInt()`
- `LovePoints`: same `int` -> `string` treatment

### Infrastructure

- PDO bindings: pass cost/milli_score/love_points as `string`
- PDO fetch: read them as `string` (PostgreSQL `NUMERIC` returns strings via PDO)

### User Interface

- API JSON responses: cost/milli_score/love_points become JSON strings (breaking change)
- CLI table output: no visible change (strings display the same)
- Web templates: no visible change

### Tests

- All fixtures and assertions for `MilliScore`, `LovePoints`, and cost switch from `int` to `string`
- CostTest expected values become string comparisons

### Dependencies

- `bcmath` PHP extension required (usually bundled, verify in Docker/CI)

## End-game research (v1 historical data, 2005-2012)

### Love point economy

- Passive PA generation caps at ~5,500 PA/hour (`ExpoSeuil(5500, 6, diff)` ceiling)
- Over 7 years (~61,320 hours) that's ~337 million PA max from passive generation
- Top v1 player (kismi) had ~567 billion PA remaining, combat stealing was the dominant PA source
- v1 ended with 2,415 members and ~2.3 trillion total PA across all players

### Realistic Heart levels (highest-cost organ to overflow)

| Heart level   | Cost for that level | Cumulative cost (all levels) |
|---------------|---------------------|------------------------------|
|            30 |        16.3 million |                  ~33 million |
|            40 |       888.6 million |                 ~1.8 billion |
|            50 |        48.5 billion |                ~98.7 billion |
|            55 |       358.5 billion |                 ~729 billion |
|            60 |       2.65 trillion |                ~5.4 trillion |
| 98 (overflow) |    4.75 quintillion |                  unreachable |

Even assuming 10x the richest player's balance (~5.7T lifetime PA), that only reaches Heart level ~60.

### Build times (from monolith formulas)

- Organs use `ExpoSeuil` (bounded ceiling): Heart caps at ~2.7 days, Legs at ~11.6 days per upgrade
- Techniques use `expo` (unbounded): Leap at level 57 would take ~304 billion years
- Build time is not the bottleneck for organs, but love point cost is
