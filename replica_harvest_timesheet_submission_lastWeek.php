<?php
require_once( 'connection.php' );

//User Object-List of Users
$range2=Harvest_Range::lastWeek("EST");
$users=$api->getUsers();

$users_Data=$users->data;

$projects=$api->getProjects();
$projects_Data=$projects->data;
$count=0;

$User_ID_List= array();
$Final_User_ID_List=array();
$Final_User_Names_List=array();
$range=Harvest_Range::thisWeek("EST",Harvest_Range::MONDAY);



//Looping through the Project Object
foreach($projects_Data as $key2=>$value2){

$project_status=$value2->get("active");

$project_billable=$value2->get("billable");

//Criteria-Takes only Active Projects
if(($project_status=="true") ){
$project_id=$value2->get("id");
 
//Getting Project Entries so that we can get Information about user-id's and whether timesheet was approved (is-closed)
$project_entries=$api->getProjectEntries($project_id,$range);


$project_entries_Data=$project_entries->data;


//Looping through Project Entries
foreach($project_entries_Data as $key3=>$value3){

$user_id=$value3->get("user-id");
$approved=$value3->get("is-closed");

//Criteria-Timesheets not approved or not Submitted This Week
if($approved=="false"){
echo "Not Approved ".$user_id;

//Trying to get the User Object to filter out Admins
$getUser=$api->getUser($user_id);
$getUser_Data=$getUser->data;

$admin=$getUser_Data->get("is-admin");


//Is-Admin Criteria
//Creating an Array of User ID's and performing a check to ensure that there are no duplicates when pushing a new element Into an Array.
if($admin=="false"){
echo $getUser_Data->get("first-name");

echo "<br>";
echo $getUser_Data->get("email");
echo "<br>";

    if(!in_array($user_id, $User_ID_List, true)){
        array_push($User_ID_List, $user_id);
    }



}

}

echo "<br>";
}



}

}

// The User ID List is a list of users whose Timesheet for this week has not been approved and are not Admins.
echo "The User ID List is: ";

echo "<br>";

var_dump($User_ID_List);

echo "<br";

//Checking each user in the User ID List whether their Timesheet was approved the last week and whether they entered Billable Hours.
foreach($User_ID_List as $value){

$getUserEntries=$api->getUserEntries($value,$range2);
$getUserEntries_Data=$getUserEntries->data;
$billable_count=0;


echo "User Data: ".$value;
echo "<br>";
var_dump($getUserEntries_Data);
echo "<br>";


foreach($getUserEntries_Data as $key3=>$value3){
$approved_user=$value3->get("is-closed");
$project_id=$value3->get("project-id");



if($approved_user=="true"){
echo "<br>It is approved!!";

echo "<br>The Project ID is: ".$project_id;


$projects=$api->getProject($project_id);


$projects_Data=$projects->data;
echo "<br>Project Information";
var_dump($projects_Data);
echo "<br>";
$project_billable=$projects_Data->get("billable");



if (($project_billable=="true")&&($billable_count<1)){
echo "It is True!!";

array_push($Final_User_ID_List, $value);
$billable_count+=1;
}

else{

echo "It is False";
}




}
else {
echo "<br>It's Not Approved!!";
}





}


}

echo "<br>The Final User List Is: ";

foreach($Final_User_ID_List as $value){
echo $value;

echo "<br>";

}


?>