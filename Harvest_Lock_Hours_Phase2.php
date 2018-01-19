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


$startLastMonth = mktime(0, 0, 0, date("m") - 2, 1, date("Y"));
$endLastMonth = mktime(0, 0, 0, date("m") -1, 0, date("Y"));

$unixdate_1=strtotime($ThreeWeeks);
$unixdate_2=strtotime($TwoWeeks);

$range_test= new Harvest_Range ( $unixdate_1, $unixdate_2);

$range=Harvest_Range::lastMonth("EST",Harvest_Range::MONDAY);
//Getting the List of Users in order to extract the User ID


$users=$api->getUsers();
	$users_Data=$users->data;

foreach ($users_Data as $key => $value) {
	$user_id=$value->get("id");

//Calling the User Entries Method to get the is-closed property
$user_entries=$api->getUserEntries($user_id,$range);	
$user_entries_data=$user_entries->data;

echo "<br>User ID: ".$user_id;

var_dump($user_entries_data);

foreach ($user_entries_data as $key2 => $value2) {

$approved=$value2->get("is-closed");

if($approved=="false"){

	echo "<br>Timesheet Not Approved";
}
}

}


?>