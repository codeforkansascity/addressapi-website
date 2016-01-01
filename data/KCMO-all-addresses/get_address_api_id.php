<?php


/*
 * http://v2-try8.api.localhost/address-attributes/V0/210%20W%2019TH%20TER?city=Kansas%20City&state=mo
 *
 */

require '/var/www/vendor/autoload.php';
require '/var/www/config/config.php';
require './AddressStandardizationSolution.php';

global $DB_NAME;
global $DB_USER;
global $DB_PASS;
global $DB_HOST;

class fix_id
{

    function connect_to_db()
    {

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

    function __construct()
    {

        $this->dbh = $this->connect_to_db();
        $address_alias = new \Code4KC\Address\AddressAlias($this->dbh, true);
        $address = new \Code4KC\Address\Address($this->dbh, true);

        $this->address_converter = new AddressStandardizationSolution();

        $sql = 'SELECT id, address_api_id, kiva_pin, city_apn, addr, fraction, prefix, street, street_type, suite, city, state, zip
FROM tmp_kcmo_all_addresses';

        $query = $this->dbh->prepare("$sql  -- " . __FILE__ . ' ' . __LINE__);

        try {
            $query->execute();
        } catch (PDOException  $e) {
            error_log($e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__);
            //throw new Exception('Unable to query database');
            return false;
        }

        $update_query = $this->dbh->prepare('UPDATE tmp_kcmo_all_addresses SET address_api_id = :address_api_id , zip = :zip WHERE id = :id;');

        $row = 0;
        $count = 0;

        while ($address_rec = $query->fetch(PDO::FETCH_ASSOC)) {
            $row++;

            if (!empty($address_rec['address_api_id']))
                continue;
            if (substr($address_rec['city_apn'], 0, 2) != 'JA') {  // skip non jackson county
                continue;
            }

            $id = $address_rec['id'];

            $single_line_address = '';
            $single_line_address .= $address_rec['addr'];
            $single_line_address .= !empty($address_rec['fraction']) ? ' ' . $address_rec['fraction'] : '';
            $single_line_address .= !empty($address_rec['prefix']) ? ' ' . $address_rec['prefix'] : '';
            $single_line_address .= !empty($address_rec['street']) ? ' ' . $address_rec['street'] : '';
            $single_line_address .= !empty($address_rec['street_type']) ? ' ' . $address_rec['street_type'] : '';
            $single_line_address .= !empty($address_rec['suite']) ? ' ' . $address_rec['suite'] : '';
            $single_line_address .= !empty($address_rec['city']) ? ', ' . $address_rec['city'] : '';
            $single_line_address .= !empty($address_rec['state']) ? ', ' . $address_rec['state'] : '';

            $single_line_address = strtoupper($single_line_address);

            $exisiting_address_alias_rec = $address_alias->find_by_single_line_address($single_line_address);

            if ($exisiting_address_alias_rec) {
                $address_id = $exisiting_address_alias_rec['address_id'];

                $exisiting_address_rec = $address->find_by_id($address_id);

                $values = array(
                    ':address_api_id' => $exisiting_address_rec['id'],
                    ':zip' => $exisiting_address_rec['zip'],
                    ':id' => $id
                );
                try {
                    $ret = $update_query->execute($values);
                } catch (PDOException  $e) {
                    print ('UPDATE ERRORE: ' . $e->getMessage() . "\n");
                }

            } else {
                print "ERROR NOT FOUND $single_line_address - " . $address_rec['city_apn'] . "\n";
            }

        }
    }

}

$a = new fix_id();
