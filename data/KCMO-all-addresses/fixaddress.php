<?php

require './AddressStandardizationSolution.php';
$address_converter = new AddressStandardizationSolution();


$address = $address_converter->AddressLineStandardization('4317 East 9th street apt 5, Kansas City, MO');

print_r($address . "\n");

exit;


http://geocoding.geo.census.gov/geocoder/geographies/address?street=210 W 19th Terr 1A&city=Kansas City&state=MO&zip=64108&benchmark=4&vintage=4&format=json



