#!/bin/sh

DIR=$(dirname $0)
cd $DIR/..

COMPOSER_ARGUMENT=''

usage()
{
    cat <<EOT
Usage:
    installer.sh [-n | --no-interraction] [--vhost]
    installer.sh -h | --help

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

echo 'Getting the sources'
git clone git://github.com/pyricau/bisouland.git
cd bisouland

# Installing requirements
if ! type composer; then
    echo 'Getting Composer, the PHP dependency manager'
    curl -sS https://getcomposer.org/installer | php
fi

if ! type gem; then
    echo 'Installing RubyGems, the Ruby package manager'
    sudo apt-get install rubygems
fi

if ! type capifony; then
    echo 'Installing Capifony, the Symfony application deployment tool'
    sudo gem install capistrano_colors
    sudo gem install capifony
fi

# Configuring the project
echo 'Configuring the project'
sh bin/configure.sh "$COMPOSER_ARGUMENT"
