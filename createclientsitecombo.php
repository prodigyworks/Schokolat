<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$clientid = $_POST['clientid'];
	
	createComboOptions("id", "name", "{$_SESSION['DB_PREFIX']}customerclientsite", "WHERE clientid = $clientid", false);
?>