<?php

$I = new PreregistrationGuy($scenario);

$I->am('Visitor');
$I->wantTo('register my email address');
$I->lookForwardTo('see a successful registration message');

$I->amOnPage('/');
$I->fillField('pre_registration[email]', 'email@example.com');
$I->click("S'inscrire");
$I->see('Votre adresse email a bien été enregistrée.');
