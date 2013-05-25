#!/bin/sh

app/console doctrine:database:drop --force --env=test
app/console doctrine:database:create --env=test
app/console doctrine:schema:create --env=test

app/console doctrine:fixtures:load --no-interaction --env=test

app/console cache:clear --env=test

bin/codecept build -c app/config/codeception.yml
bin/codecept run -c app/config/codeception.yml
