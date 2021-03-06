<?php
require_once( 'connection.php' );
	//User Object-List of Users
	$users=$api->getUsers();
	$users_Data=$users->data;
	$projects=$api->getProjects();
	$projects_Data=$projects->data;
	$role_Timesheet = 'Required';

	$User_ID_Entered_Hours_List= array();
	$User_ID_Entered_Hours_List_LastWeek= array();
	$User_ID_Final_List_3_Criteria=array();
	$User_ID_Total_List=array();
	$result=array();
	$result_Names=array();
	$range=Harvest_Range::thisWeek("EST",Harvest_Range::MONDAY);
	$range_last_week=Harvest_Range::lastWeek("EST",Harvest_Range::MONDAY);

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

	//Trying to get the specific User Object to get the Role
	$getUser=$api->getUser($user_id);
	$getUser_Data=$getUser->data;
	$user_role=$getUser_Data->get("roles");

	
	//Creating an Array-Users Entered Hours of User ID's and performing a check to ensure that there are no duplicates when pushing a new element Into an Array.
//Checking whether the user belongs to the role "Harvest Weekly Timesheet Required"
	if(strpos($user_role, $role_Timesheet) !== false){
	
	    if(!in_array($user_id, $User_ID_Entered_Hours_List, true)){
	        array_push($User_ID_Entered_Hours_List, $user_id);
	    }
	}
	}
	
	}
	}
	


	//List of Users who have entered their hours last week

//Looping through the Project Object
	foreach($projects_Data as $key2=>$value2){
	$project_status=$value2->get("active");

	//Criteria-Takes only Active Projects
	if(($project_status=="true") ){
	$project_id=$value2->get("id");
	 
	//Getting Project Entries so that we can get Information about user-id's and whether people have entered 0 hours in their Timesheet
	$project_entries=$api->getProjectEntries($project_id,$range_last_week);
	$project_entries_Data=$project_entries->data;

	//Looping through Project Entries
	foreach($project_entries_Data as $key3=>$value3){
	$user_id=$value3->get("user-id");

	//Trying to get the specific User Object to get the Role
	$getUser=$api->getUser($user_id);
	$getUser_Data=$getUser->data;
	$user_role=$getUser_Data->get("roles");

	
	//Creating an Array-Users Entered Hours of User ID's and performing a check to ensure that there are no duplicates when pushing a new element Into an Array.
//Checking whether the user belongs to the role "Harvest Weekly Timesheet Required"
	if(strpos($user_role, $role_Timesheet) !== false){
	
	    if(!in_array($user_id, $User_ID_Entered_Hours_List_LastWeek, true)){
	        array_push($User_ID_Entered_Hours_List_LastWeek, $user_id);
	    }
	}
	}
	
	}
	}


	

	//Creating a List of all users who belong to the "Harvest Weekly Timesheet Required" Role
	$count=0;
	foreach($users_Data as $key4=>$value4){
	$user_id_total=$value4->get("id");
	$user_role=$value4->get("roles");
	if(strpos($user_role, $role_Timesheet) !== false){
	 array_push($User_ID_Total_List, $user_id_total);
	$count+=1;

	}
	}



	
	
	//Array Difference -To get the list of Users who have entered 0 hours in their Timesheet This Week
	$result=  array_diff ( $User_ID_Total_List,$User_ID_Entered_Hours_List );
	echo "<br>The List of Users Who Have Entered 0 Hours in their Timesheet are: <br>";
	$count_result=0;


//Iterating over each user on this list- $result and checking whether they exist on this list- $User_ID_Entered_Hours_List_LastWeek
//If the element exists than it is pushed into a new list--The 3rd Criteria
foreach ($result as  $value) {
	if(in_array($value, $User_ID_Entered_Hours_List_LastWeek, true)){
	        array_push($User_ID_Final_List_3_Criteria, $value);
	    }
}









//Header for the email
$headers = 'From: noreply@wardpeter.com' . "\r\n" .
    'Reply-To: noreply@wardpeter.com' . "\r\n" .
     "Content-type: text/html; charset=\"UTF-8\"; format=flowed \r\n";
    'X-Mailer: PHP/' . phpversion();



    $subject_individual="Hi, it’s mid-week and you have no hours entered in your timesheet.  ".

//Body for the Individual Emails
$message1_individual = "<html><head></head><body>";
$message2_individual = "<b>Please start the Harvesting for this week</b>";
$message3_individual="Click here to go into <a href=$harvest_url>Harvest</a>";
$message4_individual="<b>Top tip:</b> You can enter time on your phone, by downloading the app.</body></html>";
$message5_individual="Thank you for cooperation ";
$message6_individual="SoHo Billing";




//If the Final Array is empty->The System doesn't need to send Individual Emails	
if(!empty($User_ID_Final_List_3_Criteria)){	
	//Inserting the names in a new Array
	foreach ($User_ID_Final_List_3_Criteria as $key6 => $value6) {
	//echo $value6;
	//echo "<br>";
	$count_result=$count_result+1;
$getUser=$api->getUser($value6);
$getUser_Data=$getUser->data;
$First_Name=$getUser_Data->get("first-name");
$Last_Name=$getUser_Data->get("last-name");
$User_Email_Address=$getUser_Data->get("email");
$to_individual=$User_Email_Address;

$subject_individual="Hi  ".$First_Name. ", it’s mid-week and you have no hours entered in your timesheet. ";


$Final_String_Individual=$message1_individual.$message2_individual."<br><br>".$message3_individual."<br>".$message4_individual."<br>".$message5_individual."<br>".$message6_individual;

mail($to_individual, $subject_individual, $Final_String_Individual, $headers);

	}

	echo "<br>The number of Users who have entered 0 hours in their Timesheet: ".$count_result; 
	






}

///////////////////////////////////////////////////////////////////////////////////////////////





	?>





?>