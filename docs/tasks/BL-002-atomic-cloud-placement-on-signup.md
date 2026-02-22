# Atomic cloud placement on signup (monolith)

## Problem

`apps/monolith/phpincludes/inscription.php` registers a new player in two separate steps:

1. `INSERT INTO membres (id, pseudo, mdp, ...)` — no `nuage`/`position` columns, so the new row lands at the column defaults: `nuage=1, position=1`
2. `GiveNewPosition($id)` — a separate function that computes the real cloud coordinate and `UPDATE`s the row

Between steps 1 and 2 the player occupies (1, 1) temporarily. The `UNIQUE(nuage, position)` constraint means that **any** player already at (1, 1) — whether placed there by Qalin's atomic CTE or left over from a previous test run — makes step 1 fail immediately with a unique constraint violation. `GiveNewPosition` is never reached.

This is always position (1, 1) because the column defaults are hardwired to that value.

In contrast, Qalin's `PdoPgSaveNewPlayer` uses a single CTE that computes and inserts the position atomically, so a player is never in an intermediate state.

## Solution

Replace the two-step INSERT + `GiveNewPosition` call in `inscription.php` with a single CTE-based `INSERT` that mirrors `PdoPgSaveNewPlayer`:

```sql
WITH last_cloud_coordinates_x AS (
    SELECT COALESCE(MAX(nuage), 1) AS cloud_coordinates_x
    FROM membres
),
total_players_on_last_cloud AS (
    SELECT COUNT(*) AS total
    FROM membres
    WHERE nuage = (SELECT cloud_coordinates_x FROM last_cloud_coordinates_x)
),
available_cloud_coordinates_x AS (
    SELECT CASE
        WHEN (SELECT total FROM total_players_on_last_cloud) >= 9
        THEN (SELECT cloud_coordinates_x FROM last_cloud_coordinates_x) + 1
        ELSE (SELECT cloud_coordinates_x FROM last_cloud_coordinates_x)
    END AS cloud_coordinates_x
),
update_cloud_counter AS (
    UPDATE nuage
    SET nombre = (SELECT cloud_coordinates_x FROM available_cloud_coordinates_x)
    WHERE id = '00000000-0000-0000-0000-000000000002'
        AND (SELECT total FROM total_players_on_last_cloud) >= 9
    RETURNING id
),
available_cloud_coordinates_y AS (
    SELECT cloud_coordinates_y
    FROM generate_series(1, 16) AS cloud_coordinates_y
    WHERE cloud_coordinates_y NOT IN (
        SELECT position
        FROM membres
        WHERE nuage = (SELECT cloud_coordinates_x FROM available_cloud_coordinates_x)
    )
    ORDER BY random()
    LIMIT 1
)
INSERT INTO membres (id, pseudo, mdp, timestamp, lastconnect, amour, nuage, position)
VALUES (
    :id,
    :pseudo,
    :mdp,
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP,
    :amour,
    (SELECT cloud_coordinates_x FROM available_cloud_coordinates_x),
    (SELECT cloud_coordinates_y FROM available_cloud_coordinates_y)
)
```

The `update_cloud_counter` CTE keeps the `nuage` config table in sync (used by `nuage.php` for display) whenever a new cloud is created.

Remove the `GiveNewPosition($id)` call from `inscription.php`. `GiveNewPosition` in `fctIndex.php` becomes dead code and can be deleted.

## Notes

- The new threshold (`>= 9`) aligns with Qalin: cloud 1 holds up to 9 players, the 10th triggers cloud 2. The old `GiveNewPosition` used `> 8` but counted the newly inserted row (at default position 1,1) as already on the cloud, effectively capping at 8.
- The `update_cloud_counter` CTE is a no-op when the cloud is not full (the `WHERE` condition is false), so it has no cost on the happy path.
- No schema change required.
