# Bisouland

Bisouland est un jeu gratuit par navigateur.

Volez des points d'amour de vos adversaires en leur envoyant des bisous !

## Installation

Pour installer Bisouland, il suffit de cloner le projet, d'utiliser
[Composer](http://getcomposer.org/) et de configurer les droits.
Le site sera ensuite disponible a travers le chemin suivant :
`web/app.php`.

### Bisouland

Installez Bisouland en clonant son depot :

    git clone https://github.com/pyricau/bisouland.git
    cd ./bisouland

### Composer

Installez [Composer](http://getcomposer.org/) et lancez la commande
d'installation :

    curl -s http://getcomposer.org/installer | php
    php composer.phar install

### Permissions

Configurez les droits :

    chmod +a "www-data allow delete,write,append,file_inherit,directory_inherit" app/cache app/logs
    chmod +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" app/cache app/logs

Si l'option `+a` n'est pas disponible sur votre plateforme,
[activez les droits ACL](https://help.ubuntu.com/community/FilePermissionsACLs)
et configurez les droits :

    setfacl -R -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs
    setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs

## Documentation

* Copyright et licence Apache 2 : `./LICENSE.md` ;
* version : `./VERSION.md` ;
* versioning, branching et API publique : `./VERSIONING.md` ;
* changelog : `./CHANGELOG.md`;
* autre : `./doc`.

## Contributer

1. Faire un Fork : https://github.com/pyricau/bisouland/fork_select ;
2. faire sa propre branche : `git checkout -b ma_branch` ;
3. faire un commit des changements : `git commit -am "Description de mes changements"` ;
4. faire un push : `git push origin ma_branche` ;
5. faire un pull request et attendre.

## Historique

* 2005 : Pierre-Yves Ricau met en place le site http://bisouland.piwai.info ;
* 2011 : il rend le projet Open Source pour qu'il soit repris par Marc Epron, Thomas Gay et Loic Chardonnet ;
* 2012 : sortie de la version 2.
