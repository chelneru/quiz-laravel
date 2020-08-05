# SAGA Installation Process

### setting up the machine and apache2
``
sudo apt-get update
``  
``
sudo apt-get dist-upgrade    ////(optional)  
``  
``
sudo a2enmod rewrite
``  
``
systemctl restart apache2
``  
``
mysql_secure_installation
``
#### install phpmyadmin
``
sudo apt-get install phpmyadmin
``  
``
sudo nano /etc/apache2/apache2.conf   (add "Include /etc/phpmyadmin/apache.conf" at the end)
``  
``
sudo service apache2 restart
``  

#### installing php 7.4 
``
  sudo apt-get update
  ``  
  ``
  sudo apt -y install software-properties-common
  ``  
  ``
  sudo add-apt-repository ppa:ondrej/php 
  ``  
  ``
  sudo apt-get update
  ``  
  ``
  sudo apt -y install php7.4
``  
``
php -v
``  
For more detials: https://computingforgeeks.com/how-to-install-php-on-ubuntu/    --install php 7.4

#### installing required extensions
``
sudo apt-get install -y php7.4-{bcmath,bz2,intl,gd,mbstring,mysql,zip,curl}
``  
``
sudo apt install php7.4-common openssl php7.4-json
``  
``
sudo apt install php-xml
``  


### create user with sudo (it is recommended to do the next operations with an user different than root)
``
adduser <user_name>
``  
``
usermod -aG sudo alin
``  
``
su - <user_name>
``  
``
sudo ls -la /root   --TEST
``  

### get the latest code for SAGA

  cd /var/www/
  
  sudo git clone https://github.com/pmpapad/saga-project.git
  
  cd saga-project
  
  sudo usermod -a -G www-data `whoami`
  
  sudo chown root:root /var/www
  
  sudo chmod 755 /var/www/
  
  sudo chown -R www-data:www-data /var/www/saga-project
  
 
  sudo chmod -R 774 /var/www/saga-project

### file permissions
Inside project folder :
sudo chown -R www-data: storage
sudo chmod -R 755 storage

sudo apt install composer


#### prepare database


change root password after login to the mysql (initial password is found in /root folder)

``
UPDATE mysql.user SET authentication_string = PASSWORD('password-here')
WHERE User = 'root' AND Host = 'localhost';
FLUSH PRIVILEGES;
``

create user for the saga application to access the database. Also create the database

``
CREATE USER 'saga_app'@'localhost' IDENTIFIED BY 'password-for-app-here';
CREATE DATABASE `saga-project` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
use 'saga-project';
GRANT SELECT, INSERT,UPDATE, DELETE ON `saga-project`.* TO 'saga_app'@'localhost';
//create user to access phpmyadmin interface
CREATE USER 'alin'@'%' IDENTIFIED BY 'user-password-here';
GRANT ALL PRIVILEGES ON *.* TO 'alin'@'%';
``

### make sure phpmyadmin uses the same PHP version as the application

#### increase upload size limit to import database
sudo nano /etc/php/7.4/apache2/php.ini   (search for upload_max_filesize)
sudo service apache2 restart

Now the database can be imported into the saga-project database

### set up apache to redirect / to the application

sudo nano /etc/apache2/sites-enabled/000-default.conf

We need to change them to point the public folder in saga-project public folder

DocumentRoot /var/www/saga-project/public
<Directory /var/www/saga-project/public>

Also we need to add the following two lines inside of that same Directory tag

RewriteEngine On
RewriteBase /var/www/saga-project/public
sudo service apache2 restart

### the .env file

on the current folder there should be an .env.prod.example (or similar name containing prod)
create a file called `.env` by duplicating the aforementioned file

Run `php artisan key:generate` inside the application project
set the APP_URL to the current domain
set database info

DB_DATABASE=

DB_USERNAME=

DB_PASSWORD=

set mail info:
MAIL_DRIVER=smtp

MAIL_HOST=

MAIL_PORT=

MAIL_USERNAME=

MAIL_PASSWORD=

MAIL_ENCRYPTION=


set if should use https (make sure https is activated if you use this)
ENABLE_HTTPS=false

set captcha keys for registration. The debug ones are used when the application is on debug mode.
CAPTCHA_SITE_KEY=
CAPTCHA_SERET_KEY=

CAPTCHA_SITE_KEY_DEBUG=
CAPTCHA_SERET_KEY_DEBU=

set google analytics for thids domain


for HTTPS in /etc/apache2/sites-available/ create a new .conf file with the name <domain>.conf(copy from the 000-default.conf file )
Inside the file add ServerName your_domain; under the first lines
Enable this new configuration : sudo a2ensite <your_domain>.conf
sudo service apache2 restart
Then check https://www.digitalocean.com/community/tutorials/how-to-secure-apache-with-let-s-encrypt-on-ubuntu-18-04

