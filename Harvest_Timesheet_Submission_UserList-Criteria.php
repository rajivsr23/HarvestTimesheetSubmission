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
//echo "Stm #1: Not Approved: ".$user_id;

//Trying to get the User Object to filter out Admins
$getUser=$api->getUser($user_id);
$getUser_Data=$getUser->data;

$admin=$getUser_Data->get("is-admin");
$user_active=$getUser_Data->get("is-active");

//Is-Admin Criteria
//Creating an Array of User ID's and performing a check to ensure that there are no duplicates when pushing a new element Into an Array.
if(($admin=="false") && ($user_active=="true")){
//echo "Stm #2: This user is not an Admin and is an active user: ".$getUser_Data->get("first-name");

//echo "<br>";
//echo "Stm #3: Email:". $getUser_Data->get("email");
//echo "<br>";

    if(!in_array($user_id, $User_ID_List, true)){
        array_push($User_ID_List, $user_id);
    }



}

}

//echo "<br>";
}



}

}

// The User ID List is a list of users whose Timesheet for this week has not been approved and are not Admins.
echo " The Users List for this week (Whose Timesheets for this week are not approved and they are not admins): ";

echo "<br>";

foreach($User_ID_List as $value){

$getUser=$api->getUser($value);
$getUser_Data=$getUser->data;
echo " ".$getUser_Data->get("first-name");
echo " " .$getUser_Data->get("last-name");

echo "<br>";



//echo $value;

//echo "<br>";

}

//echo "<br";

//echo "Stm #5: End of This Week's List..............";

//Checking each user in the User ID List whether their Timesheet was approved the last week and whether they entered Billable Hours.
foreach($User_ID_List as $value){

$getUserEntries=$api->getUserEntries($value,$range2);
$getUserEntries_Data=$getUserEntries->data;
$billable_count=0;


//echo "Stm #6:User Data: ".$value;
//echo "<br>";
//var_dump($getUserEntries_Data);
//echo "<br>";


foreach($getUserEntries_Data as $key3=>$value3){
$approved_user=$value3->get("is-closed");
$project_id=$value3->get("project-id");

//Users whose Timesheet was approved Last Week

if($approved_user=="true"){
echo "<br>Stm #7: Last week's Timesheet was approved!!";

echo "<br>Stm #8:The Project ID is: ".$project_id;

//To find out whether the Project is billable
$projects=$api->getProject($project_id);


$projects_Data=$projects->data;
echo "<br>Stm #9:Project Information";
var_dump($projects_Data);
//echo "<br>";
$project_billable=$projects_Data->get("billable");


//Checking whether the project is billable and if the user has entered billable hours, it pushes the User ID into a new Array. Checks This condition only Once
if (($project_billable=="true")&&($billable_count<1)){
echo "<br>Stm #10: The project is billable";

array_push($Final_User_ID_List, $value);
$billable_count+=1;
}

else{

echo "Stm #11: The project is not billable";
}




}
else {
//echo "<br>Stm #12: Last Week's Timesheet was not Approved!!";
}





}


}

echo "<br>From the above list, the list of users whose Last Week's Timesheet was not approved and they entered billable hours last week. ";

foreach($Final_User_ID_List as $value){
echo $value;

echo "<br>";

}


?>