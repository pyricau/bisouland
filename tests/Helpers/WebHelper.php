<?php

namespace Codeception\Module;

use Codeception\Module as BaseWebGuy;

/**
 * @author loic.chardonnet <loic.chardonnet@gmail.com>
 */
class WebHelper extends BaseWebGuy
{
    /**
     * @param string $username
     */
    public function loginAs($username) {
        $guy = $this->getModule('PhpBrowser');
        $guy->amOnPage('/login');
        $guy->fillField('_username', $username);
        $guy->fillField('_password', 'password');
        $guy->click('_submit');
    }
}
