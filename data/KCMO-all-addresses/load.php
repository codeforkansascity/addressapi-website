<?php

function connect_to_db()
{

require '/var/www/vendor/autoload.php';
require '/var/www/config/config.php';
    
    global $DB_NAME;
    global $DB_USER;
    global $DB_PASS;
    global $DB_HOST;

    print "\npgsql:host=localhost; dbname=$DB_NAME, $DB_USER, $DB_PASS\n";
    try {
        $dbh = new PDO("pgsql:host=localhost; dbname=$DB_NAME", $DB_USER, $DB_PASS);
    } catch (PDOException $e) {
        error_log($e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__);
        return false;
    }

    return $dbh;
}

$dbh = connect_to_db();

$row = 1;

ini_set("auto_detect_line_endings", true);

// Build preparied statement

            $names = '';
            $values = '';                                                                               // Build it
            $sep = '';
	    $fields = array(
		'kiva_pin' => 'kiva_pin',
		'city_apn' => 'city_apn',
		'addr' => 'addr',
		'fraction' => 'fraction',
		'prefix' => 'prefix',
		'street' => 'street',
		'street_type' => 'street_type',
		'suite' => 'suite'	
	    );

            foreach ($fields AS $f => $v) {
                $names .= $sep . $f;
                $values .= $sep . ':' . $f;
                $sep = ', ';
            }

            $sql = 'INSERT INTO hud_addresses (' . $names . ') VALUES (' . $values . ')';
            $add_query = $dbh->prepare("$sql  -- " . __FILE__ . ' ' . __LINE__);

if (($handle = fopen("KCMO_Address_11_24_2015.csv", "r")) !== FALSE) {
$row = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $row++;
        if ($row == 1) continue;

	$data[7] = array_key_exists(7,$data) ? $data[7] : '';
	$new_rec = array();
	$new_rec[':kiva_pin'] = $data[0];
	$new_rec[':city_apn'] = $data[1];
	$new_rec[':addr'] = $data[2];
	$new_rec[':fraction'] = $data[3];
	$new_rec[':prefix'] = $data[4];
	$new_rec[':street'] = $data[5];
	$new_rec[':street_type'] = $data[6];
	$new_rec[':suite'] = $data[7];

        try {
		 $ret = $add_query->execute($new_rec);
		if ( !$ret ) {
                        print_r($new_rec);
			var_dump($ret);
            		print("\nROW=$row\n----------------------------------\n " );
		}
        } catch (PDOException  $e) {
            die("ROW=$row " . $e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__);
                             
        }
            
    }
}
fclose($handle);
