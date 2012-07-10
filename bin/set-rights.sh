#!/bin/sh

setfacl -R -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs
setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs