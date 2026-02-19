# Qalin

**Qalin** (pronounced *câlin*) stands for **Quality Assurance Local Interface Nudger**.

It is the QA application for BisouLand (`apps/qa`): a **Test Control Interface** that lets
anyone (developers, QA, designers, product) to reach any game state instantly, without
having to play the game for real to get there.

## Why it exists

Want to verify that blowing a Smooch works? To do that you need to have built one first.
To build a Smooch, you need your Mouth at level 6. Here is what each upgrade costs and
how long it takes for them to complete:

| Mouth level | Cost to next level | Completion time |
|-------------|--------------------|-----------------|
|           1 |                299 |             1 s |
|           5 |              1,478 |     22 min 28 s |

But to pay for those upgrades you need Love Points (LP). Your Heart generates LP over
time. The higher its level, the more it produces per hour:

| Heart level | LP generated / hr | Cost to next level | Completion time  |
|-------------|-------------------|--------------------|------------------|
|           1 |                14 |                150 |              1 s |
|           5 |             1,657 |                739 | 1 hr 11 min      |
|          10 |             3,019 |              5,460 | 8 hr 50 min      |

LP generation plateaus around 5,000/hr. The upgrade cost does not.<sup>*</sup>

Starting fresh with 300 LP, here is the breakdown per upgrade:

| Upgrade    |       Cost | Waiting for LP | Waiting for completion |
|:----------:|-----------:|---------------:|-----------------------:|
| Heart 1→2  |        150 |             0s |                     1s |
| Heart 2→3  |        223 |         16m 0s |                    11s |
| Heart 3→4  |        333 |        26m 45s |                 5m  0s |
| Heart 4→5  |        496 |        21m 12s |                26m 24s |
| Heart 5→6  |        739 |         7m 12s |             1h 11m     |
| Heart 6→7  |      1,103 |             0s |             2h 19m     |
| Heart 7→8  |      1,645 |             0s |             3h 44m     |
| Heart 8→9  |      2,454 |             0s |             5h 21m     |
| Heart 9→10 |      3,660 |             0s |             7h 4m      |
| Mouth 1→2  |        299 |             0s |                     1s |
| Mouth 2→3  |        446 |             0s |                     1s |
| Mouth 3→4  |        665 |             0s |                    49s |
| Mouth 4→5  |        991 |             0s |                 6m 27s |
| Mouth 5→6  |      1,478 |             0s |                22m 28s |
| **Total**  | **15,182** |     **1h 11m** |            **20h 43m** |

<sup>*</sup> Completion time assumes no Soup technique.

Nearly a day, and most of it is just watching completion timers tick.
We've spent 15,000 LP to be able to build a Smooch,
which by the way grants us 15 Score Points (SC).

Ready to blow kisses now? Well not quite: Players aren't able to blow kisses
(and be kissed) when they have under 50 SP.

The grind is not done yet.

So how do you manually test the app? Do you play for days, hoping nobody wipes the
database in the meantime?

No. You use a Test Control Interface.

Qalin lets you define **Actions** and **Scenarios** that skip the time gates, the costs,
and the prerequisites, and drop you straight into the game state that matters for your
test case.

Need Heart at level 42? `instant-free-upgrade`. Bim.
Need to verify cloud-leaping works? `UnlockLeap` scenario. Bam.
Need to check kiss blowing for early game balance? `UnlockKissBlowing` scenario. Boom.

This is why it exists. This is the power of a Test Control Interface.

## Actions and Scenarios

**Actions** are atomic operations: they invoke a single application use case directly,
bypassing game restrictions (costs, completion times, etc).

**Scenarios** are composed sequences of actions that bring the game to a specific,
meaningful state in one call, named after what they represent in the domain.

## Interfaces

Qalin exposes the same actions and scenarios through multiple interfaces, so everyone
can use it in the way that suits them:

* CLI
* Web
* API
* Testsuite

### CLI

For developers who live in the terminal.

```console
make qalin
make qalin arg='action:sign-up-new-player <username> <password>'
make qalin arg='action:instant-free-upgrade <username> <upgradable> [--levels=N]'
```

![Qalin CLI screenshot](./000-qalin/qalin-cli.png)

### Web

For designers and product who prefer a browser, for example:

* http://localhost:43010/actions/sign-up-new-player
* http://localhost:43010/actions/instant-free-upgrade

![Qalin Web screenshot](./000-qalin/qalin-web.png)

### API

For bots, scripts, and HTTP clients, for example:

* `POST http://localhost:43010/actions/sign-up-new-player`
* `POST http://localhost:43010/actions/instant-free-upgrade`

### Testsuite

For automated tests. `TestKernelSingleton` boots the real application once per run and
exposes:

| Method            | Returns                                     |
|-------------------|---------------------------------------------|
| `application()`   | `ApplicationTester` (wraps the CLI)         |
| `httpClient()`    | HTTP client pointed at the Web/API          |
| `actionRunner()`  | runs use cases directly, no HTTP or CLI     |
| `pdo()`           | direct database connection                  |

Reusable scenarios (e.g. `SignUpNewPlayer::run()`, `LogInPlayer::run()`) combine these
to set up test state in a single call.

## Inspiration

Qalin is inspired by **QAAPI**, a Test Control Interface built at Bumble Inc. and described
by Sergey Ryabko in
[API for QA: Testing features when you have no access to code](https://medium.com/bumble-tech/api-for-qa-testing-features-when-you-have-no-access-to-code-3892456aa2de)
(2021).

The core idea is the same: rather than touching the database directly or bending
production code to fit a test scenario, you expose a dedicated set of controlled
operations that anyone on the team can call: developers, QA, designers, product,
and automated tests alike.
