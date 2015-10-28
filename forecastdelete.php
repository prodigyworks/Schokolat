<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$id = $_POST['id'];
	
	$qry = "DELETE FROM {$_SESSION['DB_PREFIX']}eventforecast WHERE id = $id";
	$result = mysql_query($qry);
	
	mysql_query("COMMIT");
?>
