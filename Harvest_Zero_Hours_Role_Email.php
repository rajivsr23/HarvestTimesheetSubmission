
	<?php
	require_once( 'connection.php' );
	//User Object-List of Users
	$users=$api->getUsers();
	$users_Data=$users->data;
	$projects=$api->getProjects();
	$projects_Data=$projects->data;
	$role_Timesheet = 'Required';

	$User_ID_Entered_Hours_List= array();
	$User_ID_Total_List=array();
	$result=array();
	$result_Names=array();
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
	$user_department=$getUser_Data->get("roles");

	//Is-Admin Criteria
	//Creating an Array-Users Entered Hours of User ID's and performing a check to ensure that there are no duplicates when pushing a new element Into an Array.
	if(strpos($user_department, $role_Timesheet) !== false){
	
	    if(!in_array($user_id, $User_ID_Entered_Hours_List, true)){
	        array_push($User_ID_Entered_Hours_List, $user_id);
	    }
	}
	}
	
	}
	}
	
	


	//Creating a List of Active Users in Harvest
	$count=0;
	foreach($users_Data as $key4=>$value4){
	$user_id_total=$value4->get("id");
	$user_admin_total=$value4->get("is-admin");
	$user_active_total=$value4->get("is-active");
	$user_department=$value4->get("roles");
	if(strpos($user_department, $role_Timesheet) !== false){
	 array_push($User_ID_Total_List, $user_id_total);
	$count+=1;

	}
	}



	
	
	//Array Difference
	$result=  array_diff ( $User_ID_Total_List,$User_ID_Entered_Hours_List );
	echo "<br>The List of Users Who Have Entered 0 Hours in their Timesheet are: <br>";
	$count_result=0;
	
	//Inserting the names in a new Array
	foreach ($result as $key6 => $value6) {
	//echo $value6;
	//echo "<br>";
	$count_result=$count_result+1;
$getUser=$api->getUser($value6);
$getUser_Data=$getUser->data;
$First_Name=$getUser_Data->get("first-name");
$Last_Name=$getUser_Data->get("last-name");
echo " ".$getUser_Data->get("first-name");
echo " " .$getUser_Data->get("last-name");
echo "<br>";
$Full_Name=$First_Name." ".$Last_Name;

array_push($result_Names, $Full_Name);
	}
	echo "<br>The number of Users who have entered 0 hours in their Timesheet: ".$count_result; 
	

//Emailing Section


	$to="67e3cba4.sohodragon.com@amer.teams.ms";
$subject = 'Test-Users Not submitted their Timesheet';
$message1="These users didn’t submit their timesheets on Friday.";

$message2="Can their line managers remind them that there timesheets need to be complete by Friday.";



$headers = 'From: noreply@wardpeter.com' . "\r\n" .
    'Reply-To: noreply@wardpeter.com' . "\r\n" .
     "Content-type: text/html; charset=\"UTF-8\"; format=flowed \r\n";
    'X-Mailer: PHP/' . phpversion();



$result=implode("<br> ",$result_Names); //////



//String Concatenation

$new_string=$message1."<br> ".$result. "<br>".$message2;

mail($to, $subject, $new_string, $headers);


	?>