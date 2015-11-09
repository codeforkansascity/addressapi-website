# TIGER STUFF




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
