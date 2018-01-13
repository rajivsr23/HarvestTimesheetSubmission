


	<?php
	require_once( 'connection.php' );
	
	$users=$api->getUsers();
	$users_Data=$users->data;
	
	foreach ($users_Data as $key => $value) {
		echo "<br> ".$value->get("first-name");
	}

	?>
