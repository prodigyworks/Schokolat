<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$id = $_POST['id'];
	$eventid = $_POST['eventid'];
	$date = convertStringToDate($_POST['date']);
	$amount = $_POST['amount'];
	
	if ($id == 0) {
		$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}eventforecast 
				(
					eventid, forecastdate, amount
				)
				VALUES
				(
					$eventid, '$date', $amount
				)";
		
	} else {
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}eventforecast SET
				forecastdate = '$date',#
				amount = $amount
				WHERE id = $id";
	}
	
	$result = mysql_query($qry);
	
	mysql_query("COMMIT");
?>