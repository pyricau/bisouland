<?php

/*
 * This file is part of the bisouland project.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bisouland\Test\Functional\Api;

use Bisouland\Test\Functional\ControllerTestCase;

class UserTest extends ControllerTestCase
{
    public function testCreateUser()
    {
        $this->givenThisParameter('username', 'john.doe');
        $this->andThisParameter('plain_password', 'Pa$$w0rd!');

        $this->whenRequesting('POST', '/api/users');

        $this->thenItShouldSucceed();
    }
}
