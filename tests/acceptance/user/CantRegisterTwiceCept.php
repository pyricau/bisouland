<?php

$I = new UserGuy($scenario);

$I->am('Registered User');
$I->wantTo('fail the registration');
$I->lookForwardTo('see a failure message');

$I->amOnPage('/register');
$I->fillField('fos_user_registration_form[username]', 'already.registered');
$I->fillField('fos_user_registration_form[email]', 'already.registered@example.com');
$I->fillField('fos_user_registration_form[plainPassword][first]', 'password');
$I->fillField('fos_user_registration_form[plainPassword][second]', 'password');
$I->click('Enregistrer');
$I->dontSee('L\'utilisateur a été créé avec succès');
$I->see('Le nom d\'utilisateur est déjà utilisé');
$I->see('L\'adresse e-mail est déjà utilisée');
