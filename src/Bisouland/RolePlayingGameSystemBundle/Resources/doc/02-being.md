# 02 - Being

La classe `Being` decrit le personnage. Il possede :

* un nom qui sera unique dans la base de donnees ;
* des points de vie ;
* un attribut d'attaque ;
* un attribut de defense ;
* un attribut de constitution.

Les attributs permettent de calculer ensuite un bonus qui s'ajoute a
differentes etapes et qui font du personnage quelqu'un de special.

## Attributs

Le bonus d'attributs se calcule ainsi :

* 3 = -4 en bonus ;
* 4&5 = -3 ;
* 6 &7 = -2 ;
* 8&9 = -1 ;
* 10&11 = 0 ;
* 12&13 = +1 ;
* 14&15 = +2 ;
* 16&17 = +3 ;
* 18 = +4.

### Attack

Le bonus d'attaque permet d'avoir plus de chances de reussir une attaque (hit).
Il s'ajoute aussi aux degats infliges (defenderLoss).

### Defense

Le bonus de defense permet d'avoir plus de chances d'esquiver un attaque (hit).

### Constitution

Le bonus de constitution s'ajoute au nombre de points de vie lors de la creation
du personnage.
Ils permettent aussi de reduire le nombre de points gagnes lorsque le personnage
se fait attaquer (attackerEarning).

## Factory

Le generateur de personnage permet d'automatiser la creation de celui-ci, avec
les etapes suivantes :

1. generation du nom ;
2. tirage au des des attributs ;
3. initialisation des points de vie.

Pour les attributs, 4d6 sont jetes, le plus faible est laisse de cote. Cela
permet d'avoir entre -4 a 4 en bonus, avec plus de chances de faire des
resultats moyens (pour plus de precisions : voir attributes dans
[D&D statistics](https://klubkev.org/~ksulliva/ralph/dnd-stats.html).
