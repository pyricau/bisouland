# 01 - Introduction

RolePlayingGameSystem est un bundle fait par et pour Bisouland.
Il permet de mettre en place des personnages avec des attributs et des points
de vie, afin de les faire combattre.

Pour cela, des pseudo-tables sont fournies ainsi que des generateurs.

## Installation

Pour les utiliser, il suffit de creer dans votre propre bundle
des entites qui heriteront des pseudo tables. Il existe une relation `OneToMany`
entre `Being` et `Attack`, donc il vous faudra aussi les implementer.

L'exemple suivant vous permetta de mieux comprendre comment faire.

### Being

```php
<?php

namespace Acme\ExampleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Bisouland\RolePlayingGameSystemBundle\Entity\Being;
use Bisouland\RolePlayingGameSystemBundle\Entity\Attack;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class MyBeing extends Being
{
    /**
     * @ORM\OneToMany(targetEntity="Acme\ExampleBundle\Entity\MyAttack", mappedBy="attacker")
     */
    protected $attacksDone;

    /**
     * @ORM\OneToMany(targetEntity="Acme\ExampleBundle\Entity\MyAttack", mappedBy="defender")
     */
    protected $defensesDone;
}
```

### Attack

```php
<?php

namespace Acme\ExampleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Bisouland\RolePlayingGameSystemBundle\Entity\Attack;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class MyAttack extends Attack
{
    /**
     * @ORM\ManyToOne(targetEntity="Acme\ExampleBundle\Entity\MyBeing", inversedBy="attacksDone")
     * @ORM\JoinColumn(name="attacker_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attacker;

    /**
     * @ORM\ManyToOne(targetEntity="Acme\ExampleBundle\Entity\MyBeing", inversedBy="defensesDone")
     * @ORM\JoinColumn(name="defender_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $defender;
}
```
