<?php
	include("system-db.php");
	
	start_db();

	$_SESSION['SESS_EVENT_ID'] = $_POST['eventid'];
	
	header("location: epos.php?w=" . time());
?>