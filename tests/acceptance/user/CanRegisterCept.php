<?php

$I = new UserGuy($scenario);

$I->am('Visitor');
$I->wantTo('register');
$I->lookForwardTo('see a successful message');

$I->amOnPage('/register/');
$I->fillField('fos_user_registration_form[username]', 'to.register');
$I->fillField('fos_user_registration_form[email]', 'to.register@example.com');
$I->fillField('fos_user_registration_form[plainPassword][first]', 'password');
$I->fillField('fos_user_registration_form[plainPassword][second]', 'password');
$I->click("Enregistrer");
$I->see('votre compte est maintenant activé.');
