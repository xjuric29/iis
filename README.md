# IIS - project name
## Installation
#### Debian/Ubuntu
```bash
sudo apt update && sudo apt upgrade
sudo apt install apache2 mariadb-server php composer
sudo mysql_secure_installation
sudo mkdir /var/git
sudo chmod 777 /var/git
cd /var/git
git clone git@github.com:xjuric29/iis.git
cd iis/web-project
composer install	# Install nette dependencies to vendor dir.
sudo ln -s /var/git/iis/web-project/www/ /var/www/iis
sudo printf "\n# IIS project\nlocalhost\tiis\n" >> /etc/hosts
sudo ln -s /var/git/iis/dev/apache2/sites-available/iis.conf /etc/apache2/sites-available/
sudo a2ensite iis.conf
sudo systemctl reload apache2
sudo mysql < /var/git/iis/dev/sql/init_db.sql
```

Now is the Nette blank project running on http://iis!
