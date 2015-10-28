<?php
	require_once("system-db.php");
	
	start_db();
	
	if (strpos($_SERVER['HTTP_USER_AGENT'], "iPhone") ||
		strpos($_SERVER['HTTP_USER_AGENT'], "Android")) {
		header("location: epos.php?ts=" . time());
		
	} else {
		header("location: sales.php");
	}
?>