<?php

$I = new UserGuy($scenario);

$I->am('Logged in User');
$I->wantTo('sign out');
$I->lookForwardTo('see the login link');

$I->loginAs('to.logout');
$I->amOnPage('/');
$I->click('Déconnexion');
$I->see('Connexion');
