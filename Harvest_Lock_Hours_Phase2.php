<?php
//Testing the Dates

ini_set("display_errors","1");
ERROR_REPORTING(E_ALL);
require_once("connection.php");

//updateEntry-Day Entry Object-User Entries
//PHP-Two weeks past
$dateInTwoWeeks = strtotime('-2 weeks');
$today=strtotime("now");

$today_date=date("d-m",$today);


echo $today_date;

echo "<br>Date Two weeks ago..";

$TwoWeeks=date("d-m",$dateInTwoWeeks);

echo $TwoWeeks;
//Harvest_Range (String $from, String $to);




?>