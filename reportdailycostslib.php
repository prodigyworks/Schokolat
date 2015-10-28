<?php
	require_once('reportdailycostslibdata.php');
	
	$pdf = new SalesReport( 'P', 'mm', 'A4', $_POST['datefrom']);
	$pdf->Output();
?>