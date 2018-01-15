<?php
require_once( 'connection.php' );
	
//Header for the email
$headers = 'From: noreply@wardpeter.com' . "\r\n" .
    'Reply-To: noreply@wardpeter.com' . "\r\n" .
     "Content-type: text/html; charset=\"UTF-8\"; format=flowed \r\n";
    'X-Mailer: PHP/' . phpversion();



    $subject_individual="Hi";

//Body for the Individual Emails
$message="Hello";
$harvest_url="https://sohodragon.harvestapp.com/time/week";

//Body for the Individual Emails
$message1_individual = "<html><head></head><body>";
$message2_individual = "<b>Please start the Harvesting for this week</b>";
$message3_individual="Click here to go into <a href=$harvest_url>Harvest</a>";
$message4_individual="<b>Top tip:</b> You can enter time on your phone, by downloading the app.</body></html>";
$message5_individual="Thank you for cooperation ";
$message6_individual="SoHo Billing";

$to_individual="rr@sohodragon.com";

$subject_individual="Hi, it’s mid-week and you have no hours entered in your timesheet. ";


$Final_String_Individual=$message1_individual.$message2_individual."<br><br>".$message3_individual."<br>".$message4_individual."<br>".$message5_individual."<br>".$message6_individual;

mail($to_individual, $subject_individual, $Final_String_Individual, $headers);

	

	echo "Mail Sent";
	


	?>





