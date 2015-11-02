
create a directoroy backup and then copy the dumps to it

cp address_api-20151012.sql.gz code4kc-20151012.sql.gz  /Users/paulb/Projects/code4kc/address-api/sites/v2-try4/kRy4Bg/backups



sudo su -

apt-get install postgresql-contrib
apt-get install postgis
apt-get install postgresql-9.3-postgis-2.1
apt-get install unzip wget

vi /etc/postgresql/9.3/main/postgresql.conf

Change the listen_addresses to your IP address

listen_addresses = '192.168.56.1,192.168.56.104,localhost'      # what IP address(es) to listen on;


sudo vi /etc/postgresql/9.3/main/pg_hba.conf 

# Remote from Vagrant Host
host    all             all             all                     password


/etc/init.d/postgresql stop

/etc/init.d/postgresql start

exit

sudo su - postgres
psql

create database paul WITH ENCODING 'UTF8' TEMPLATE=template0; 

create database code4kc  WITH ENCODING 'UTF8' TEMPLATE=template0;
create database address_api  WITH ENCODING 'UTF8' TEMPLATE=template0;
\c code4kc
CREATE EXTENSION postgis;
CREATE EXTENSION postgis_topology;
CREATE EXTENSION postgis_sfcgal;
CREATE EXTENSION fuzzystrmatch;
CREATE EXTENSION address_standardizer;
SELECT postgis_full_version();
\q


Now restore databases

   cd /var/www/backup
   zcat address_api-20151012.sql.gz | psql address_api
   zcat code4kc-20151012.sql.gz | psql code4kc 


Now create website


cd /etc/apache2/sites-available

cat > 002-dev-api.conf

<VirtualHost *:80>

    ServerAdmin webmaster@localhost
    ServerName dev-api.codeforkc.org
    DocumentRoot /var/www/address-api.dev/webroot

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

#   Header set Access-Control-Allow-Origin "*"
#   Header set Access-Control-Allow-Credentials "true"
#   Header set Access-Control-Allow-Methods "POST, GET, OPTIONS"


    <Directory /var/www/address-api.dev/webroot>
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

cd ../sites-enabled/
ln -s ../sites-available/002-dev-api.conf .
apache2ctl restart


On Host

add the following to /etc/hosts


192.168.56.104 dev-api.codeforkc.org


On Guest

sudo su - postgres

createuser c4kc

psql

\s
ALTER USER c4kc with encrypted password 'data';
GRANT ALL PRIVILEGES ON DATABASE address_api TO c4kc;
GRANT ALL PRIVILEGES ON DATABASE code4kc TO c4kc;

CREATE DATABASE aa_api  WITH ENCODING 'UTF8' TEMPLATE=template0;
CREATE DATABASE aa_gis  WITH ENCODING 'UTF8' TEMPLATE=template0;
GRANT ALL PRIVILEGES ON DATABASE aa_api TO c4kc;
GRANT ALL PRIVILEGES ON DATABASE aa_gis TO c4kc;






-- CREATE EXTENSION postgis_tiger_geocoder; Did not do this for address api

SELECT postgis_full_version();

GRANT USAGE ON SCHEMA tiger TO PUBLIC;
GRANT USAGE ON SCHEMA tiger_data TO PUBLIC;
GRANT SELECT, REFERENCES, TRIGGER    ON ALL TABLES IN SCHEMA tiger TO PUBLIC;
GRANT SELECT, REFERENCES, TRIGGER    ON ALL TABLES IN SCHEMA tiger_data TO PUBLIC;
GRANT EXECUTE    ON ALL FUNCTIONS IN SCHEMA tiger TO PUBLIC;
ALTER DEFAULT PRIVILEGES IN SCHEMA tiger_dataGRANT SELECT, REFERENCES    ON TABLES TO PUBLIC;


\l
\t
\a
\o /gisdata/nationscript.sh
        SELECT loader_generate_nation_script('sh');
        \o


cd /gisdata
vi nationscript.sh

Change password 123
Location of PG to /usr/bin



sh nationscript.sh




-----

update pg_database set encoding = pg_char_to_encoding('UTF8') where datname = 'aa_api';
update pg_database set encoding = pg_char_to_encoding('UTF8') where datname = 'aa_gis';
update pg_database set encoding = pg_char_to_encoding('UTF8') where datname = 'address_api';
update pg_database set encoding = pg_char_to_encoding('UTF8') where datname = 'code4kc';
update pg_database set encoding = pg_char_to_encoding('UTF8') where datname = 'aa_api';
# Sample Apache Configuration

````
# -----------------------------------------------
# ADDRESS_API.CODEFORKC.ORG
# -----------------------------------------------
<VirtualHost *:80>
    ServerName address_api.codeforkc.org
    DocumentRoot /var/www/address_api.org/webroot
    DirectoryIndex index.php

    Header set Access-Control-Allow-Origin "*"
    Header set Access-Control-Allow-Credentials true
    Header set Access-Control-Allow-Methods "POST, GET, OPTIONS"

    <Directory "/var/www/address_api.org/webroot/">
       RewriteEngine On
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
       Options -Indexes FollowSymLinks
       Order allow,deny
       Allow from all
    </Directory>

</VirtualHost>
````
