#!/bin/sh

app/console doctrine:database:drop --force --env=test
app/console doctrine:database:create --env=test
app/console doctrine:schema:create --env=test

app/console doctrine:fixtures:load --no-interaction --env=test

app/console cache:clear --env=test

for bundle in BisoulandUserBundle
do
    echo "Testing ${bundle}..."
    bin/behat -c=app/config/behat.yml @${bundle} $*
done
