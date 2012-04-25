# VERSIONING

Ce fichier indique le versioning, le branching et l'API publique du projet
Bisouland.

## Versioning semantique

Le [Versioning semantique](http://semver.org/) est strictement suivi.

## Modele de Branching

Le modele de branching est inspire par cet atricle:
[A successful Git branching model](http://nvie.com/posts/a-successful-git-branching-model/):
* La branche __master__ est celle qui est la principale et stable ;
* la branche __develop__ est celle qui est la principale et instable ;
* les branches __hotfix-*__ sont des corrections de bugs venant de __master__ ;
* les branches de fonctionnalites viennent de __develop__ ;
* les __release-*__ sont des branches de support de la production venant de __develop-*__.

## API publique

According to the Semantic Versioning Specification, a public API must be
defined.
In this project, the `design` folder will be marked as the public API:
* any correction should increment the patch version (Z);
* any addition of file/line should increment the minor version (Y);
* any removal and modification of file/line should increment the major version (Y).
