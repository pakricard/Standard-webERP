<?php
// geocode.php

//$PageSecurity = 3;
$Title = _('Geocode Generate');

include ('includes/session.php');
include ('includes/header.php');
//include ('includes/SQL_CommonFunctions.inc');

$SQL = "SELECT * FROM geocode_param WHERE 1";
$ErrMsg = _('An error occurred in retrieving the information');
$Resultgeo = DB_query($SQL, $ErrMsg);
$Row = DB_fetch_array($Resultgeo);

$api_key = $Row['geocode_key'];
$center_long = $Row['center_long'];
$center_lat = $Row['center_lat'];
$map_height = $Row['map_height'];
$map_width = $Row['map_width'];
$map_host = $Row['map_host'];

define("MAPS_HOST", $map_host);
define("KEY", $api_key);

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/maintenance.png" title="' . _('Geocode Setup') . '" alt="" />' . ' ' . _('Geocoding of Customers and Suppliers')  . '</p>';

// select all the customer branches
$SQL = "SELECT * FROM custbranch WHERE 1";
$ErrMsg = _('An error occurred in retrieving the information');
$Result = DB_query($SQL, $ErrMsg);

// select all the suppliers
$SQL = "SELECT * FROM suppliers WHERE 1";
$ErrMsg = _('An error occurred in retrieving the information');
$Result2 = DB_query($SQL, $ErrMsg);

// Initialize delay in geocode speed
$delay = 0;
$base_url = "https://" . MAPS_HOST . "/maps/api/geocode/xml?address=";

// Iterate through the customer branch rows, geocoding each address


while ($Row = DB_fetch_array($Result)) {
  $geocode_pending = true;

  while ($geocode_pending) {
    $address = urlencode($Row["braddress1"] . "," . $Row["braddress2"] . "," . $Row["braddress3"] . "," . $Row["braddress4"]);
    $id = $Row["branchcode"];
    $debtorno =$Row["debtorno"];
    $request_url = $base_url . $address . '&key=' . KEY . '&sensor=true';

    echo '<br \>', _('Customer Code'), ': ', $id;


    $xml = simplexml_load_string(utf8_encode(file_get_contents($request_url))) or die("url not loading");
//    $xml = simplexml_load_file($request_url) or die("url not loading");

    $status = $xml->status;

    if (strcmp($status, "OK") == 0) {
      // Successful geocode
      $geocode_pending = false;
      $coordinates = $xml->GeocodeResponse->result->geometry->location;
      $coordinatesSplit = explode(",", $coordinates);
      // Format: Longitude, Latitude, Altitude
      $lat = $xml->result->geometry->location->lat;
      $lng = $xml->result->geometry->location->lng;

      $query = sprintf("UPDATE custbranch " .
             " SET lat = '%s', lng = '%s' " .
             " WHERE branchcode = '%s' " .
 	     " AND debtorno = '%s' LIMIT 1;",
             ($lat),
             ($lng),
             ($id),
             ($debtorno));

      $Update_result = DB_query($query);

      if ($Update_result==1) {
      echo '<br />'. 'Address: ' . $address . ' updated to geocode.';
      echo '<br />'. 'Received status ' . $status . '<br />';
	}
    } else {
      // failure to geocode
      $geocode_pending = false;
      echo '<br />' . 'Address: ' . $address . _('failed to geocode.');
      echo 'Received status ' . $status . '<br />';
    }
    usleep($delay);
  }
}

// Iterate through the Supplier rows, geocoding each address
while ($Row2 = DB_fetch_array($Result2)) {
  $geocode_pending = true;

  while ($geocode_pending) {
    $address = $Row2["address1"] . ",+" . $Row2["address2"] . ",+" . $Row2["address3"] . ",+" . $Row2["address4"];
    $address = urlencode($Row2["address1"] . "," . $Row2["address2"] . "," . $Row2["address3"] . "," . $Row2["address4"]);
    $id = $Row2["supplierid"];
    $request_url = $base_url . $address . '&key=' . KEY . '&sensor=true';

    echo '<p>' . _('Supplier Code: ') . $id;

    $xml = simplexml_load_string(utf8_encode(file_get_contents($request_url))) or die("url not loading");
//    $xml = simplexml_load_file($request_url) or die("url not loading");

    $status = $xml->status;

    if (strcmp($status, "OK") == 0) {
      // Successful geocode
      $geocode_pending = false;
      $coordinates = $xml->GeocodeResponse->result->geometry->location;
      $coordinatesSplit = explode(",", $coordinates);
      // Format: Longitude, Latitude, Altitude
      $lat = $xml->result->geometry->location->lat;
      $lng = $xml->result->geometry->location->lng;


      $query = sprintf("UPDATE suppliers " .
             " SET lat = '%s', lng = '%s' " .
             " WHERE supplierid = '%s' LIMIT 1;",
             ($lat),
             ($lng),
             ($id));

      $Update_result = DB_query($query);

      if ($Update_result==1) {
      echo '<br />' . 'Address: ' . $address . ' updated to geocode.';
      echo '<br />' . 'Received status ' . $status . '<br />';
      }
    } else {
      // failure to geocode
      $geocode_pending = false;
      echo '<br />' . 'Address: ' . $address . ' failed to geocode.';
      echo '<br />' . 'Received status ' . $status . '<br />';
    }
    usleep($delay);
  }
}
echo '<br /><div class="centre"><a href="' . $RootPath . '/GeocodeSetup.php">' . _('Go back to Geocode Setup') . '</a></div>';
include ('includes/footer.php');
?>