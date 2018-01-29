	<?php
	ini_set("display_errors","1");
ERROR_REPORTING(E_ALL);
	require_once( 'connection.php' );

//Harvest Range Format: 20160901 :YYYYMMDD
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

//Statements to Calculate This Sunday Date

    $Current = Date('N'); //N-1-Monday...7-Sunday
$DaysToSunday = 7 - $Current;
$Sunday = Date('M-d', StrToTime("+ {$DaysToSunday} Days"));
$unix_Sunday= StrToTime("+ {$DaysToSunday} Days");
//Calculating This Monday's Date
//$DaysToMonday=1-$Current;
//$Monday=Date("M-d", StrToTime("last Monday");





$unix_Monday= StrToTime('Monday this week');

$Monday = date('M-d',$unix_Monday);

echo "<br>The Date this Monday is: ".$Monday;

echo "<br>The Date This Sunday is: ".$Sunday;

echo "<br>The date on Sunday Two Weeks before was...";

$DateTwoWeeksBefore_Sunday=getTwoWeeksBefore($Sunday);
echo "<br>".$DateTwoWeeksBefore_Sunday;
echo "<br>Formatted Harvest Sunday Date: ".$DateTwoWeeksBefore_Sunday;

echo "<br>The date on Monday Two Weeks before was...";
$DateTwoWeeksBefore_Monday=getTwoWeeksBefore($Monday);
echo "<br>".$DateTwoWeeksBefore_Monday;
echo "<br>Formatted Harvest Monday Date: ".$DateTwoWeeksBefore_Monday;

function getTwoWeeksBefore($DateThisWeek){

$DateTwoWeeksBefore=date('Ymd', strtotime('-2 week', strtotime($DateThisWeek)));

echo "<br>".$DateTwoWeeksBefore;
return $DateTwoWeeksBefore;
}


//Since the script is running on Monday...

	//Should be Last Week
	$range_last_week=Harvest_Range::lastWeek("EST",Harvest_Range::MONDAY);
	//Should be Week before Last
	$range_week_before_last=new Harvest_Range($DateTwoWeeksBefore_Monday, $DateTwoWeeksBefore_Sunday);

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
	$project_entries=$api->getProjectEntries($project_id,$range_week_before_last);
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

//Statements to Calculate Sunday Date

    $Current = Date('N');
$DaysToSunday = 7 - $Current;
$Sunday = Date('M-d', StrToTime("+ {$DaysToSunday} Days"));

    $subject_individual="Please Submit Your Timesheet, Week Ending: ".$Sunday; //Sunday Date

    //Yesterday's Date-Since Email runs on Monday

   $Yesterday= date("F j", time() - 60 * 60 * 24);




	


if(empty($User_ID_Final_List_3_Criteria)){	

//If the Final List is Empty Send an Email to Teams->Nice Feature to Have
$to="67e3cba4.sohodragon.com@amer.teams.ms";
	//$to="a38c5253.sohodragon.com@amer.teams.ms ";

$subject = 'Everyone submitted their Timesheets';

$message1="All timesheets submitted this week ending on  ".$Yesterday. ". ". "Great job team." ;

$message2="Thank you";

$message3="SoHo Billing";


$new_string=$message1."<br><br> ".$message2."<br>".$message3;

mail($to, $subject, $new_string, $headers);

}

else{

foreach ($User_ID_Final_List_3_Criteria as $key6 => $value6) {
	//echo $value6;
	//echo "<br>";
	$count_result=$count_result+1;
$getUser=$api->getUser($value6);
$getUser_Data=$getUser->data;
$First_Name=$getUser_Data->get("first-name");
$Last_Name=$getUser_Data->get("last-name");

$Full_Name=$First_Name." ".$Last_Name;

array_push($result_Names, $Full_Name);
}	

	echo "<br>The number of Users who have entered 0 hours in their Timesheet: ".$count_result; 
	

//Emailing Section

	//For the Consolidated Email to Teams

//$to="rr@sohodragon.com";

$to="67e3cba4.sohodragon.com@amer.teams.ms";
	//$to="a38c5253.sohodragon.com@amer.teams.ms ";
$subject = 'Users Not submitted their Timesheet';


$message1="These users hadn't submitted their timesheets by Friday ".date("M-d", strtotime("last friday"));

$message2="Can their line managers remind them that their timesheets need to be complete by Friday.";

$message3="SoHo Billing";







$result=implode("<br> ",$result_Names); //////



//String Concatenation

$new_string=$message1."<br> ".$result. "<br>".$message2."<br><br>".$message3;

mail($to, $subject, $new_string, $headers);



}




///////////////////////////////////////////////////////////////////////////////////////////////





	?>
