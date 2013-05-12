#!/bin/sh

echo 'Getting the sources'
git clone git://github.com/pyricau/bisouland.git
cd bisouland

# Installing requierements

echo 'Getting Composer, the PHP dependency manager'
curl -sS https://getcomposer.org/installer | php

echo 'Getting npm, the Javascript package manager'
curl -sS https://npmjs.org/install.sh | sudo sh

echo 'Installing UglifyJs, the Javascript parser/compressor/beautifier toolkit'
sudo npm install -g uglify-js@1

echo 'Installing UglifyCss, the CSS parser/compressor/beautifier toolkit'
sudo npm install -g uglifycss

echo 'Installing LESS, Dynamic stylesheet language (extends CSS)'
sudo npm install -g less

# Configuring the project

echo 'Setting the rights'
setfacl -R -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs
setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs

echo 'Installing the dependencies'
./composer.phar install --dev

# Configuring Apache

echo 'Creating the vhost configuration'
sudo cat<<EOT | sudo tee /etc/apache2/sites-available/bisouland.local
<VirtualHost *:80>
    DocumentRoot "<path>/web"
    ServerAlias bisouland.local

    ErrorLog "/var/log/apache2/bisouland/error.log"
    CustomLog "/var/log/apache2/bisouland/access.log" common

    <Directory "<path>/web">
        Options -Indexes FollowSymLinks Includes ExecCGI
        AllowOverride All
        Order allow,deny
        Allow from All
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
