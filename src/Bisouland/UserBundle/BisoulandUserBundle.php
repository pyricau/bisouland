<?php

namespace Bisouland\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Integration of FOSUserBundle into the application.
 *
 * @author Loïc Chardonnet <loic.chardonnet@gmail.com>
 */
class BisoulandUserBundle extends Bundle
{
    /**
     * @{inheritdoc}
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
