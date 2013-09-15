#!/bin/sh

DIR=$(dirname $0)
cd $DIR/..

COMPOSER_ARGUMENT=''

usage()
{
    cat <<EOT
Usage:
    install.sh [-n | --no-interraction]
    install.sh -h | --help

Options:
    -h --help           Show this screen
    -n --no-interaction Do not ask any interractive question
EOT
}

if [ $# -gt 1 ]; then
    echo 'ERROR: too many arguments'
    usage
    exit 1
fi

case $1 in
    -h | --help)
        usage
        exit
        ;;
    -n | --no-interaction)
        COMPOSER_ARGUMENT='--no-interaction'
        ;;
    '')
        ;;
    *)
        echo "ERROR: unknown argument \"$1\""
        usage
        exit 1
        ;;
esac

echo 'Creating directories for temporary files'
rm -rf app/cache app/logs app/sessions
mkdir app/cache app/logs app/sessions

echo 'Setting the rights'
setfacl -R -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs app/sessions
setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs app/sessions

echo 'Installing the dependencies'
composer "$COMPOSER_ARGUMENT" install

echo 'Securing the configuration'
SECRET=`date +%s | sha256sum | base64 | head -c 32`
sed -i "s|ThisTokenIsNotSoSecretChangeIt|${SECRET}|" app/config/parameters.yml
