<?php 
	include "system-db.php";

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
	
	start_db();
	
	function getAmount($date, $productid, $eventid, $mode, $type) {
		$amount = 0;
		$sql = "SELECT IFNULL(SUM(A.amount), 0) AS amount
				FROM {$_SESSION['DB_PREFIX']}eventtransaction A 
				WHERE A.productid = $productid
				AND A.eventid = $eventid
				AND A.eventdate = '$date'
				AND A.type = '$type' ";
		
		if ($mode != 0) {
			$sql .= "AND A.eventid = $mode";
		}
		
		$itemresult = mysql_query($sql);
		
		if ($itemresult) {
			while (($itemmember = mysql_fetch_assoc($itemresult))) {
				 $amount = $itemmember['amount'];
			}
		
		} else {
			logError($sql . " - " . mysql_error());
		}
				
		return $amount;
	}
	
	$startdate = ($_GET['from']);
	$enddatetime = ($_GET['to']);
	$enddate = ($_GET['to']);
	$mode = $_GET['mode'];
	$json = array();
	
	$sql = "SELECT count(*) AS amount FROM {$_SESSION['DB_PREFIX']}event";
	$result = mysql_query($sql);
	$count = 10;

	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$count = 100 / $member['amount'];
		}
	} else {
		logError($sql . " " . mysql_error());
	}
				
	
	$sql = "SELECT id, name FROM {$_SESSION['DB_PREFIX']}product ORDER BY name";
	$result = mysql_query($sql);
	
	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$productid = $member['id'];
			$date = $startdate;
			
			$html = "<TABLE cellspacing=0 cellpadding=0 height='100%' border=1 width=100%><TR>";
			$sql = "SELECT A.id, A.name, B.stock
					FROM {$_SESSION['DB_PREFIX']}event  A
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}eventproductmatrix B
					ON B.productid = $productid
					AND B.eventid = A.id
					ORDER BY name";
			$itemresult = mysql_query($sql);
			
			//Check whether the query was successful or not
			if($itemresult) {
				while (($itemmember = mysql_fetch_assoc($itemresult))) {
					$eventid = $itemmember['id'];
					
					$sold = getAmount($date, $productid, $eventid, $mode, "S");
					$broken = getAmount($date, $productid, $eventid, $mode, "B");
					$giveaway = getAmount($date, $productid, $eventid, $mode, "G");
					$remaining = $itemmember['stock'];
					
					if ($remaining == "") {
						$remaining = "0";
					}
					
					if ($remaining <= 10) {
						$remaining = "<span style='color:red'>$remaining</span>";
					}
					
					$html .= "<TD align=center height='75px' width='" . number_format($count, 2) . "%'>$sold / $remaining</TD>";
				}
			}
			
			$html .= "</TR></TABLE>";			
			
			array_push(
					$json, 
					array(
							"id" => $productid . "_" . $date,
							"color" => "white",
							"textColor" => "blue",
						"true_start_date" => "$date",
							"start_date" => $date . " 00:00:00",
							"end_date" => $date . " 23:59:59",
							"text" => "<div class='entry'>$html</div>",
							"section_id" => $productid
						)
				);
			
		}
				
	} else {
		logError($sql . " - " . mysql_error());
	}

	mysql_query("COMMIT");
	
	echo json_encode($json);
?>