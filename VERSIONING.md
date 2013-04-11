# VERSIONING

This file explains the versioning and branching models of this project
and its public API.

## Semantic Versioning

[Semantic Versioning](http://semver.org/) is used.
For a version `X.Y.Z`, we have:
 * removal or modification of the public API will increase the major number `X`;
 * new features will increase the minor number `Y`;
 * fixes or new tests will increase the patch number `Z`.

## Branching Model

The branching model is inspired by this article:
[A successful Git branching model](http://nvie.com/posts/a-successful-git-branching-model/):
* `master` branch is the main stable one;
* `develop` is the main unstable one;
* `hotfix/*` are used to fix `master`;
* `release/*` branches are between `develop` and `master`;
* the other branches come from `develop`:
  * `feature/*` for new functionalities;
  * `test/*` for new tests;
  * `fix/*` to fix bugs only present in `develop`;
  * `refactoring/*` for code improvements and cleaning;
  * `documentation/*` for documentation.

## Public API

The public API will be defined by the game actions a user will be able to do.
