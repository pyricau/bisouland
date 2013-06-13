#!/bin/sh

VHOST=0

usage()
{
    cat <<EOT
Usage:
    install.sh [--vhost]
    install.sh -h | --help

Options:
    --vhost   Create a vhost and log files, restart Apache and add a host
    -h --help Show this screen
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
    --vhost)
        VHOST=1
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

# Installing requierements

if ! type composer; then
    echo 'Getting Composer, the PHP dependency manager'
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
fi

if ! type uglifyjs; then
    echo 'Installing UglifyJs, the Javascript parser/compressor/beautifier toolkit'
    sudo npm install -g uglify-js
fi

if ! type uglifycss; then
    echo 'Installing UglifyCss, the CSS parser/compressor/beautifier toolkit'
    sudo npm install -g uglifycss
fi

if ! type less; then
    echo 'Installing LESS, Dynamic stylesheet language (extends CSS)'
    sudo npm install -g less
fi

# Configuring the project

echo 'Setting the rights'
setfacl -R -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs app/sessions
setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs app/sessions

echo 'Installing the dependencies'
composer install --dev

# Configuring Apache
if [ $VHOST -eq 1 ]; then
    echo 'Creating the vhost configuration'
    sudo cat<<EOT | sudo tee /etc/apache2/sites-available/bisouland.local
<VirtualHost *:80>
    ServerName bisouland.local

    ErrorLog "/var/log/apache2/bisouland/error.log"
    CustomLog "/var/log/apache2/bisouland/access.log" common

    DocumentRoot "<path>/web"
    <Directory "<path>/web">
        DirectoryIndex app.php

        Options -Indexes FollowSymLinks Includes ExecCGI
        AllowOverride All
        Order allow,deny
        Allow from All

        <IfModule mod_rewrite.c>
            RewriteEngine On
        </IfModule>
    </Directory>
</VirtualHost>
EOT
    sudo sed -i "s|<path>|${PWD}|" /etc/apache2/sites-available/bisouland.local
    sudo ln -s /etc/apache2/sites-available/bisouland.local /etc/apache2/sites-enabled/bisouland.local

    echo 'Creating the log files'
    sudo mkdir /var/log/apache2/bisouland
    sudo touch /var/log/apache2/bisouland/error.log
    sudo touch /var/log/apache2/bisouland/access.log

    echo 'Restarting the web server'
    sudo /etc/init.d/apache2 restart

    echo 'Adding the hostname'
    sudo echo '127.0.0.1 bisouland.local' | sudo tee -a /etc/hosts
fi
