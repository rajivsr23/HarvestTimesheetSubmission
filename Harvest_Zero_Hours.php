<?php
require_once( 'connection.php' );

//User Object-List of Users

$users=$api->getUsers();

$users_Data=$users->data;

$projects=$api->getProjects();
$projects_Data=$projects->data;


$User_ID_Entered_Hours_List= array();
$User_ID_Total_List=array();

$range=Harvest_Range::thisWeek("EST",Harvest_Range::MONDAY);



//Looping through the Project Object
foreach($projects_Data as $key2=>$value2){

$project_status=$value2->get("active");



//Criteria-Takes only Active Projects
if(($project_status=="true") ){
$project_id=$value2->get("id");
 
//Getting Project Entries so that we can get Information about user-id's and whether people have entered 0 hours in their Timesheet
$project_entries=$api->getProjectEntries($project_id,$range);


$project_entries_Data=$project_entries->data;


//Looping through Project Entries
foreach($project_entries_Data as $key3=>$value3){

$user_id=$value3->get("user-id");




//Trying to get the specific User Object to filter out Admins
$getUser=$api->getUser($user_id);
$getUser_Data=$getUser->data;

$admin=$getUser_Data->get("is-admin");
$user_active=$getUser_Data->get("is-active");

//Is-Admin Criteria
//Creating an Array of User ID's and performing a check to ensure that there are no duplicates when pushing a new element Into an Array.
if(($admin=="false") && ($user_active=="true")){
echo "<br>Stm #2: This user is not an Admin and is an active user: ".$getUser_Data->get("first-name");

echo "<br>";
echo "Stm #3: Email:".$getUser_Data->get("email");
echo "<br>";

    if(!in_array($user_id, $User_ID_Entered_Hours_List, true)){
        array_push($User_ID_Entered_Hours_List, $user_id);
    }



}

}

echo "<br>";
}



}



// The User ID List is a list of users who have entered hours in their Timesheet
echo "<br>Stm #4: The User ID List for this week for people who have entered their hours in their Timesheet ";

echo "<br>";

foreach($User_ID_Entered_Hours_List as $value){
echo $value;

echo "<br>";

}

echo "<br>";

//Looping through the User Object

/*
$count=0;
foreach($users_Data as $key4=>$value4){
$user_id_total=$value4->get("id");
$user_active_total=$value4->get("is-active");

if($user_active_total=="true"){
 array_push($User_ID_Total_List, $user_id_total);
$count+=1;

}

}

echo "<br>The Active Users in Harvest are...";
echo "<br>";
foreach ($User_ID_Total_List as $key5 => $value5) {
echo $value5;
echo "<br>";
	
}

echo "Total Active Users ".$count;
*/

?>