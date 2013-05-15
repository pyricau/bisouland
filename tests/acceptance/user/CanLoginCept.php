<?php

$I = new UserGuy($scenario);

$I->am('Logged out User');
$I->wantTo('sign in');
$I->lookForwardTo('see my login');

$I->amOnPage('/login');
$I->fillField('_username', 'to.login');
$I->fillField('_password', 'password');
$I->click('_submit');
$I->see('to.login');
