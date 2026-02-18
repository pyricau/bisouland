# BisouLand

BisouLand is a free browser-based strategy game that turns romance into
competitive gameplay.

Join other players online as you strategically send **Kisses** to capture
**Love Points** and dominate the clouds.

Simple to learn, engaging to master, and completely free to play.

A delightfully retro online strategy game from 2005, lovingly preserved in its
original state. Experience a fascinating time capsule of early web development
and quirky game design.

![Screenshot](Screenshot.png)

## Installation

To install BisouLand, first get its sources:

```console
git clone git://github.com/pyricau/bisouland.git bisouland
cd bisouland
```

Find general documentation here: [docs](./docs/).

This is a monorepo. The `apps` folder contains the different applications:

* [Monolith](./apps/monolith/README.md):
  the main (original) BisouLand application
* [QA](./apps/qa/README.md):
  tools to keep BisouLand on its toes

The `packages` folder contains shared libraries used across apps.
See [How to manage packages](./docs/how-to/002-how-to-manage-packages.md) for details.

## Port Convention

BisouLand uses the `43YYX` port convention:

* `43` = "love you" prefix (love = 4 letters, you = 3 letters)
* `YY` = app identifier (`00` = monolith, `01` = qa, etc)
* `X` = service type (0 = web server, 1 = database, etc.)

For example:

* `43000`: Monolith web server
* `43001`: Monolith database
* `43010`: QA web server

## Further documentation

You can find more documentation at the following links:

* legal terms (Apache 2): [LICENSE](LICENSE)
* records of version changes: [CHANGELOG.md](CHANGELOG.md)
* contribution instructions: [CONTRIBUTING.md](CONTRIBUTING.md)

## Project history

* 2025: roll back to version 1, again; now version 4
* 2013: roll back to the version 1, which becomes the version 3
* 2012: release of the version 2
* 2011: Open-sourcing of the project, new team to take over the project with
  Marc Epron, Thomas Gay and Loïc Faugeron
* 2005: creation of the project by **Pierre-Yves Ricau**
