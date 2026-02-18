# Fix Love Points recalculation during blown kiss

## Problem

When blowing a kiss at an inactive player, the Love Points taken can exceed
what the spy report shows, and the effect compounds with each successive blown kiss.

Two bugs in `apps/monolith/phpincludes/attaque.php` combine to cause this:

### 1. Bisous destroyed before Love Points recalculation (order-of-operations)

When the kiss lands successfully (line 326+), the receiver's bisous are reduced first
(lines 338-342), then Love Points are recalculated using the already-reduced counts (line 356).

The Love Points generation formula is:
`diff = heart - (0.3 * peck + 0.7 * smooch + french_kiss)`

- `diff > 0` → Love Points increase passively over time
- `diff < 0` → Love Points decrease passively over time

As repeated blown kisses destroy the receiver's bisous toward zero, `diff` flips
from negative to positive. The recalculation then concludes the receiver
has been **passively gaining** Love Points the whole time they were inactive.

### 2. Receiver timestamp never updated after blown kiss (the compounding bug)

In `app.php` (line 813), `timestamp` is updated on every page load
for **signed-in players only**:

```sql
SET lastconnect = CURRENT_TIMESTAMP, timestamp = CURRENT_TIMESTAMP, amour = :amour
```

But in `attaque.php` (lines 397-412), the blown kiss UPDATE on the receiver
only saves `amour, smack, baiser, pelle` — **timestamp is not updated**.

Each successive blown kiss on an inactive player:

1. Reads `amour` (already includes previous recalculation) and `timestamp` (never updated, months old)
2. Calls `calculterAmour(amour, time() - ancient_timestamp, heart, zero_bisous)`
3. Applies passive generation for the **entire inactive duration** on top of the already-recalculated base
4. Saves the inflated `amour`, still doesn't update `timestamp`

This re-applies the full passive generation from the original timestamp on every blown kiss,
compounding each time.

## Discovered via

v1 historical observation (2005-2012): inactive players like Petrus provided
increasingly more Love Points when kissed, despite spy reports showing near-zero balance.

## Solution

When implementing blown kisses in `apps/qa`:

1. Recalculate receiver Love Points **before** destroying bisous in the kiss resolution
2. Update the receiver's `timestamp` after saving the new Love Points value
