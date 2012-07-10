# 03 - Attack

La classe `Attack` decrit une attaque. Elle possede :

* le gain de l'attaquant ;
* les degats subit par le defenseur ;
* la verification si l'attaque est critique ;
* la verification si l'attaque a reussit.

## Factory

Le generateur d'attaque permet d'automatiser la creation de celle-ci, avec
les etapes suivantes :

1. verification si l'attaque reussit (hit) ;
2. verification si l'attaque est critique (critical) ;
3. si l'attaque est reussie, calcul des degats infliges (defenderLoss) ;
4. si l'attaque est reussie, calcul du gain (attackerEarning).

### Hit

L'attaquant lance 1d20 et ajoute son bonus d'attaque et le defenseur lance 1d20
et ajoute son bonus de defense.
Si le resultat de l'attaquant est strictement superieur, alors l'attaque
reussit.

### Critical

Si le d20 de l'attaquant fait 20, l'attaque devient un coup critiue et reussit
a coup sur.

S'il fait 1, l'attaque devient un echec critique et echoue a coup sur.

### DefenderLoss

Les degats sont calcules a l'aide de 1d4 auquel s'ajoute le bonus d'attaque de
l'attaquant.
Au final, les degats seront de 1 minimum.

### AttackerEarning

Le gain est calcule a partir des degats auxquels on soustrait le bonus de
constitution du defenseur.
