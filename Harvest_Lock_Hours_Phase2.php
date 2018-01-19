<?php
require_once( 'connection.php' );

//Testing the Dates

ini_set("display_errors","1");
ERROR_REPORTING(E_ALL);
require_once("connection.php");

//updateEntry-Day Entry Object-User Entries

//PHP-Three weeks past
echo "<br>Date Three weeks ago..";
$dateInThreeWeeks = strtotime('-3 weeks');

$ThreeWeeks=date("d-m",$dateInThreeWeeks);

echo $ThreeWeeks;

//Two Weeks
echo "<br>Date Two weeks ago..";

$dateInTwoWeeks = strtotime('-2 weeks');
$TwoWeeks=date("d-m",$dateInTwoWeeks);

echo $TwoWeeks;

//Today
echo "<br>Date Today";

$today=strtotime("now");

$today_date=date("d-m",$today);


echo $today_date;






$range= new Harvest_Range ( $ThreeWeeks, $TwoWeeks);



?>