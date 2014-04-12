#!/bin/bash

dirname="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
phpunit="$dirname/../vendor/bin/phpunit"
console="$dirname/../app/console --env=test"

echo '[console] Creating the database'
$console doctrine:database:create
$console doctrine:schema:create

echo '[phpunit] Running functional tests'
$phpunit -c app

echo '[console] Destroying the database'
$console doctrine:database:drop --force
