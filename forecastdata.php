<?php 
	include("system-db.php");
	
	start_db();
	
	header('Content-Type: application/json');
	
	if (! isset($_POST['start'])) {
		$startdate = "2014-07-01";
		$enddate = "2014-08-01";
				
	} else {
		$startdate = $_POST['start'];
		$enddate = $_POST['end'];
	}
	
	$eventid = $_POST['eventid'];
	
	$sql = "SELECT A.id, A.amount, A.forecastdate 
			FROM {$_SESSION['DB_PREFIX']}eventforecast A
			WHERE eventid = $eventid
			AND (A.forecastdate <= '$enddate' AND A.forecastdate >= '$startdate')";
	$result = mysql_query($sql);	

	$json = array();

	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$line = array(
					"id"				=> $member['id'], 
					"allDay"			=> "true",
					"className"			=> "eventcat_" . $member['id'],
					"start"				=> $member['forecastdate'] . " 00:00",
					"end"				=> $member['forecastdate'] . " 00:00",
					"title"				=> $member['amount']
				);
				
			array_push($json, $line);
		}
		
	} else {
		logError($qry . " - " . mysql_error());
	}

	echo json_encode($json);
?>
