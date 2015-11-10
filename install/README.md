

# Create site

1. Clone repository

````
    git clone git@github.com:zmon/address-api-v0.git your-dir
````

2. Run composer update

````
    cd your-dir
    composer update
````

# Create image


````
    vagrant up
    vagrant ssh
````

You should now be logged into the new virtual box

# Install postgres, unzip, and wget

````
    sudo su -
    apt-get install postgresql-contrib postgis postgresql-9.3-postgis-2.1 php5-pgsql unzip wget
````


# Install module mod_headers

````
a2enmod headers
````


#  Configure PostGres 
````
vi /etc/postgresql/9.3/main/postgresql.conf
````

Change the listen_addresses to your IP address

````
listen_addresses = '192.168.56.1,192.168.56.209,localhost'      # what IP address(es) to listen on;
````


````
sudo vi /etc/postgresql/9.3/main/pg_hba.conf 
````

# Remote from Vagrant Host
````
host    all             all             all                     password
````


````
/etc/init.d/postgresql stop

/etc/init.d/postgresql start

exit
````

# Create database

````
sudo su - postgres
````

Create user

````
createuser c4kc
````


````
psql
````


# Final db
````
CREATE DATABASE c4kc_address_api  WITH ENCODING 'UTF8' TEMPLATE=template0;
ALTER USER c4kc with encrypted password 'data';
GRANT ALL PRIVILEGES ON DATABASE c4kc_address_api TO c4kc;
\c c4kc_address_api
CREATE EXTENSION postgis;
CREATE EXTENSION postgis_topology;
CREATE EXTENSION postgis_sfcgal;
CREATE EXTENSION fuzzystrmatch;
CREATE EXTENSION address_standardizer;
\q
````




# Restore databases

````
   cd /var/www/INSTALL/sql
   zcat c4kc_address_api.sql.gz | psql c4kc_address_api
````



# Set permissions
````
psql
\c c4kc_address_api

alter table  address                     OWNER TO c4kc;
alter table  address_alias               OWNER TO c4kc;
alter table  address_id_seq              OWNER TO c4kc;
alter table  address_id_seq_02           OWNER TO c4kc;
alter table  address_key_id_seq          OWNER TO c4kc;
alter table  address_keys                OWNER TO c4kc;
alter table  address_string_alias_id_seq OWNER TO c4kc;
alter table  census_attributes           OWNER TO c4kc;
alter table  city_address_attributes     OWNER TO c4kc;
alter table  county_address_attributes   OWNER TO c4kc;
alter table  county_address_data         OWNER TO c4kc;
alter table  datas                       OWNER TO c4kc;
alter table  datas_id                    OWNER TO c4kc;
alter table  datasets                    OWNER TO c4kc;
alter table  datasets_id                 OWNER TO c4kc;
alter table  fields                      OWNER TO c4kc;
alter table  fields_id                   OWNER TO c4kc;
alter table  geography_columns           OWNER TO c4kc;
alter table  geometry_columns            OWNER TO c4kc;
alter table  jd_wp                       OWNER TO c4kc;
alter table  jd_wp_id_seq                OWNER TO c4kc;
alter table  loads                       OWNER TO c4kc;
alter table  loads_id                    OWNER TO c4kc;
alter table  neighborhoods               OWNER TO c4kc;
alter table  neighborhoods_id_seq        OWNER TO c4kc;
alter table  raster_columns              OWNER TO c4kc;
alter table  raster_overviews            OWNER TO c4kc;
alter table  sources                     OWNER TO c4kc;
alter table  sources_id                  OWNER TO c4kc;
alter table  spatial_ref_sys             OWNER TO c4kc;
alter table  layer                       OWNER TO c4kc;
alter table  topology                    OWNER TO c4kc;
alter table  topology_id_seq             OWNER TO c4kc;

\d

SELECT postgis_full_version();

\q
````

exit



Now create website


````
sudo su -
cd /etc/apache2/sites-available
````

````
cat > 002-dev-api.conf

````

<VirtualHost *:80>

    ServerAdmin webmaster@localhost
    ServerName dev-api.codeforkc.org
    DocumentRoot /var/www/webroot

    # Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
    # error, crit, alert, emerg.
    # It is also possible to configure the loglevel for particular
    # modules, e.g.
    #LogLevel info ssl:warn

    ErrorLog ${APACHE_LOG_DIR}/dev-api-error.log
    CustomLog ${APACHE_LOG_DIR}/dev-api-access.log combined

    # For most configuration files from conf-available/, which are
    # enabled or disabled at a global level, it is possible to
    # include a line for only one particular virtual host. For example the
    # following line enables the CGI configuration for this host only
    # after it has been globally disabled with "a2disconf".
    #Include conf-available/serve-cgi-bin.conf

    DirectoryIndex index.php


    <Directory /var/www/webroot>
        Header set Access-Control-Allow-Origin "*"
        Header set Access-Control-Allow-Credentials "true"
        Header set Access-Control-Allow-Methods "POST, GET, OPTIONS"

        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted 

        <FilesMatch "\.php$">
            Require all granted
            SetHandler proxy:fcgi://127.0.0.1:9000
        </FilesMatch>
        
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
        Order allow,deny
        Allow from all

    </Directory>
</VirtualHost>
````

````
cd ../sites-enabled/
ln -s ../sites-available/002-dev-api.conf .
apache2ctl restart
````

On Host

add the following to /etc/hosts


````
192.168.56.209 dev.api.codeforkc.org
````



# Setup config file

````
<?php

global $DB_NAME;
global $DB_USER;
global $DB_PASS;
global $DB_HOST;

if ( !empty( $_SERVER["DB_HOST"] )) { $DB_HOST = $_SERVER["DB_HOST"]; } else { $DB_HOST = 'localhost'; }
if ( !empty( $_SERVER["DB_USER"] )) { $DB_USER = $_SERVER["DB_USER"]; } else { $DB_USER = 'c4kc'; }
if ( !empty( $_SERVER["DB_PASS"] )) { $DB_PASS = $_SERVER["DB_PASS"]; } else { $DB_PASS = 'data'; }
if ( !empty( $_SERVER["DB_NAME"] )) { $DB_NAME = $_SERVER["DB_NAME"]; } else { $DB_NAME = 'c4kc_address_api'; }
````
