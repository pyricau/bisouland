<?php

namespace Bisouland\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/** @author Loic Chardonnet <loic.chardonnet@gmail.com> */
class BisoulandUserBundle extends Bundle
{
    /** @{inheritdoc} */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
