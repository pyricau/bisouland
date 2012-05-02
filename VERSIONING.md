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

Une API publique est requise pour respecter le Versioning semantique.
Pour Bisouland, cette API sera le jeu ressenti par le joueur :

* une simple correction ou un nouveau test augmentera la version patch (Z) ;
* un ajout de fonction augmentera la version mineure (Y) ;
* la suppression ou la modification d'une fonction augmentera la version majeure (X).
