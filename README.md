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
composer install    # Install nette dependencies to vendor dir.
mkdir www/img/usr   # User image dir.
chmod 777 www/img/usr
sudo ln -s /var/git/iis/web-project/www/ /var/www/iis
sudo printf "\n# IIS project\nlocalhost\tiis\n" >> /etc/hosts
sudo ln -s /var/git/iis/dev/apache2/sites-available/iis.conf /etc/apache2/sites-available/
sudo a2ensite iis.conf
sudo systemctl reload apache2
sudo mysql < /var/git/iis/dev/sql/init_db.sql
```

Now is the iis project running on http://iis!

## Useful tips
- Set the "post_max_size" and "upload_max_filesize" to **20M** in php.ini file. This ensure that add/edit ticket page will work correct with bigger size of images.
- Create folder **web-project/www/img/usr** if it is not exists and change permissions to **777**.
