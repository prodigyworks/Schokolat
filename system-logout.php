<?php
	//Include database connection details
	require_once('system-db.php');
	
	logout();

	header("location: system-login.php");
?>
