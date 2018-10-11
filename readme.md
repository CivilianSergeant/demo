#Setup Project Readme

Upload All files to /var/www/html/project-dir/

setup virtual host directory path to /var/www/html/project-dir/public

Give Following Permissions (If CentOS use 'apache' instead of www-data)

* sudo chgrp -R www-data storage bootstrap/cache
  
* sudo chmod -R ug+rwx storage bootstrap/cache