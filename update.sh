php ./upgrade_database.php
chown www-data:www-data -R ./

# For whatever reason, the delivery via sendfile requires execute permissions on nix.
chmod 500 -R ./
chmod 700 -R ./logs
chmod 700 -R ./errors
chmod 700 -R ./library/uploads
