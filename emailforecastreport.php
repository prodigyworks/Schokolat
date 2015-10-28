<?php
	require_once("system-db.php");
	require_once('reportdailycostslibdata.php');
		
	start_db();
	
	$file = "uploads/emailforecast-" . session_id() . "-" . time() . ".pdf";
		
	$report = new SalesReport( 'P', 'mm', 'A4', date("d/m/Y"));
	$report->Output($file, "F");	
		
    sendRoleMessage("FORECASTALERT", "Forecast Results", "Forecast information attached", array($file));
?>