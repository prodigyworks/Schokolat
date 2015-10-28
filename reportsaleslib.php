<?php
	require_once('system-db.php');
	require_once('pdfreport.php');
	require_once("simple_html_dom.php");
	
	class SalesReport extends PDFReport {
		
		function GetEventName($eventid) {
			$name = "Unknown";
			$sql = "SELECT A.name 
				    FROM {$_SESSION['DB_PREFIX']}event A 
					WHERE A.id = $eventid";
			$result = mysql_query($sql);
			
			if ($result) {
				while (($member = mysql_fetch_assoc($result))) {
					$name = $member['name'];
				}
			}
			
			return $name;
		}
		
		function AddPage($orientation='', $size='') {
			parent::AddPage($orientation, $size);
			
			$this->Image("images/logomain2.png", 132.6, 1);
			
			$size = $this->addText( 10, 5, "Report Sales", 12, 4, 'B');
			
			if ($_POST['eventid'] == "0") {
				$size = $this->addText( 10, 14, "Event : All", 10, 4, 'B') + 5;
												
			} else {
				$size = $this->addText( 10, 14, "Event : " . $this->GetEventName($_POST['eventid']), 10, 4, 'B') + 5;
			}
			
			$size = $this->addText( 10, 19, "Between : " . $_POST['datefrom'] . " and " . $_POST['dateto'], 10, 4, 'B') + 5;
			
			$this->SetFont('Arial','', 6);
				
			$cols = array( 
					"Product"  => 102,
					"Sold"  => 22,
					"Broken"  => 22,
					"Demo"  => 22,
					"Cost" => 22
			);
			
			$this->addCols($size, $cols);

			$cols = array(
					"Product"  => "L",
					"Sold"  => "R",
					"Broken"  => "R",
					"Demo"  => "R",
					"Cost" => "R"
				);
			$this->addLineFormat( $cols);
			$this->SetY(36);
		}
		
		function __construct($orientation, $metric, $size) {
			$dynamicY = 0;
			
	        parent::__construct($orientation, $metric, $size);
	        
	        $this->SetAutoPageBreak(true, 30);
			$this->AddPage();
			
			try {
				$startdate = convertStringToDate($_POST['datefrom']);
				$enddate = convertStringToDate($_POST['dateto']);
				
				if ($_POST['eventid'] != "0") {
					$eventid = $_POST['eventid'];
					$sql = "SELECT A.name, A.retailprice,
						   (SELECT SUM(B.amount) FROM {$_SESSION['DB_PREFIX']}eventtransaction B WHERE B.productid = A.id AND B.eventid = $eventid AND B.eventdate BETWEEN '$startdate' AND '$enddate') AS sold,
						   (SELECT SUM(C.amount) FROM {$_SESSION['DB_PREFIX']}eventtransaction C WHERE C.productid = A.id AND C.eventid = $eventid AND C.eventdate BETWEEN '$startdate' AND '$enddate') AS broken,
						   (SELECT SUM(D.amount) FROM {$_SESSION['DB_PREFIX']}eventtransaction D WHERE D.productid = A.id AND D.eventid = $eventid AND D.eventdate BETWEEN '$startdate' AND '$enddate') AS demo
						    FROM {$_SESSION['DB_PREFIX']}product A 
							GROUP BY A.name
							ORDER BY A.name";
										
				} else {
					$sql = "SELECT A.name, A.retailprice,
						   (SELECT SUM(B.amount) FROM {$_SESSION['DB_PREFIX']}eventtransaction B WHERE B.productid = A.id AND B.eventdate BETWEEN '$startdate' AND '$enddate' and B.type = 'S') AS sold,
						   (SELECT SUM(C.amount) FROM {$_SESSION['DB_PREFIX']}eventtransaction C WHERE C.productid = A.id AND C.eventdate BETWEEN '$startdate' AND '$enddate' and C.type = 'B') AS broken,
						   (SELECT SUM(D.amount) FROM {$_SESSION['DB_PREFIX']}eventtransaction D WHERE D.productid = A.id AND D.eventdate BETWEEN '$startdate' AND '$enddate' and D.type = 'G') AS demo
						    FROM {$_SESSION['DB_PREFIX']}product A 
							GROUP BY A.name
							ORDER BY A.name";
				}
				
				$result = mysql_query($sql);
				
				if ($result) {
					$total = 0;
					$totalsold = 0;
					$totalbroken = 0;
					$totaldemo = 0;
					
					while (($member = mysql_fetch_assoc($result))) {
						$sold = $member['sold'] != "" ? $member['sold'] : 0;
						$broken = $member['broken'] != "" ? $member['broken'] : 0;
						$demo = $member['demo'] != "" ? $member['demo'] : 0;

						$line=array(
								"Product"  => $member['name'],
								"Sold"  => " " . $sold,
								"Broken"  => " " . $broken,
								"Demo"  => " " . $demo,
								"Cost" => "£ " . number_format($member['retailprice'] * ($broken + $sold + $demo), 2)
							);
							
						$this->addLine( $this->GetY(), $line );
						
						$total += ($member['retailprice'] * ($broken + $sold + $demo));
						$totalsold += $sold;
						$totalbroken += $broken;
						$totaldemo += $demo;
					}
					
					$line=array(
							"Product"  => "Total : ",
							"Sold"  => " " . $totalsold,
							"Broken"  => " " . $totalbroken,
							"Demo"  => " " . $totaldemo,
							"Cost" => "£ " . number_format($total, 2)
						);
						
					$this->addLine( $this->GetY() + 4, $line );
					
				} else {
					logError($sql . " - " . mysql_error());
				}
				
			} catch (Exception $e) {
				logError($e->getMessage());
			}
		}
	}
	
	start_db();
	
	$pdf = new SalesReport( 'P', 'mm', 'A4');
	$pdf->Output();
?>