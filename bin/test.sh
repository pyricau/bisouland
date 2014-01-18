#!/bin/sh

DIR=$(dirname $0)
cd $DIR/..

app/console doctrine:database:drop --force --env=test
app/console doctrine:database:create --env=test
app/console doctrine:schema:create --env=test

app/console doctrine:fixtures:load --no-interaction --env=test

app/console cache:clear --env=test

for feature_path in `find src/ -path '*Features'`
do
    bundle=$(echo $feature_path | sed -e 's~.*src/\(.*\)/Features~\1~' | sed -e 's~/~~')
    echo "Testing $bundle"
    php vendor/bin/behat -c=app/config/behat.yml "@$bundle"
done
