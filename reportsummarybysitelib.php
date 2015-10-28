<?php
	require_once('system-db.php');
	require_once('pdfreport.php');
	require_once("simple_html_dom.php");
	
	class SalesReport extends PDFReport {
		private $fromdate;
		private $todate;
		
		function AddPage($orientation='', $size='') {
			parent::AddPage($orientation, $size);
			
			$this->Image("images/logomain2.png", 132.6, 1);
			
			$size = $this->addText( 10, 5, "Report Summary Site", 12, 4, 'B');
			
			$size = $this->addText( 10, 14, "Event : " . GetEventName($_POST['eventid']), 10, 4, 'B') + 5;
			$size = $this->addText( 10, 19, "Between : " . date("d-M-Y", strtotime($this->fromdate)) . " and " . date ("d-M-Y", strtotime($this->todate)), 10, 4, 'B') + 5;
			
			$this->SetFont('Arial','', 6);
			
			$cols = array(
					GetEventName($_POST['eventid']) => 40,
					"SOLD" => 30,
					"BROKEN" => 30,
					"DEMO" => 30,
					"BALANCE" => 30,
					"SALES" => 30
				);
				
			$this->addCols($size, $cols);

			$cols = array(
					GetEventName($_POST['eventid']) => "L",
					"SOLD" => "R",
					"BROKEN" => "R",
					"DEMO" => "R",
					"BALANCE" => "R",
					"SALES" => "R"
				);
				
			$this->addLineFormat( $cols);
			$this->SetY(36);
		}
		
		function __construct($orientation, $metric, $size, $startdate, $enddate) {
			$this->fromdate = convertStringToDate($startdate);
			$this->todate = convertStringToDate($enddate);
			$eventid = $_POST['eventid'];
			
			$dynamicY = 0;
			
	        parent::__construct($orientation, $metric, $size);
	        
	        $this->SetAutoPageBreak(true, 30);
			$this->AddPage();
			
			try {
				$total = array();
				$total[0] = 0;
				$total[1] = 0;
				$total[2] = 0;
				$total[3] = 0;
				$total[4] = 0;
				
				if ($eventid == 0) {
					$sql = "SELECT A.id, A.name, A.retailprice, B.stock 
							FROM {$_SESSION['DB_PREFIX']}product A 
							LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}eventproductmatrix B
							ON B.productid = A.id
							ORDER BY A.name";
					
				} else {
					$sql = "SELECT A.id, A.name, A.retailprice, B.stock 
							FROM {$_SESSION['DB_PREFIX']}product A 
							LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}eventproductmatrix B
							ON B.productid = A.id
							AND B.eventid = $eventid
							ORDER BY A.name";
				}
		
				$result = mysql_query($sql);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						$productid = $member['id'];
						$productname = $member['name'];
						$stock = $member['stock'];
						$retailprice = $member['retailprice'];
						$sold = 0;
						$broken = 0;
						$demo = 0;
				
						if ($eventid == 0) {
							$sql = "SELECT 
									IFNULL(SUM(B.amount), 0) AS amount 
									FROM {$_SESSION['DB_PREFIX']}eventtransaction B 
									WHERE B.productid = $productid 
									AND B.type = 'S'
									AND B.eventdate BETWEEN '{$this->fromdate}' AND '{$this->todate}'";
								
						} else {
							$sql = "SELECT 
									IFNULL(SUM(B.amount), 0) AS amount 
									FROM {$_SESSION['DB_PREFIX']}eventtransaction B 
									WHERE B.productid = $productid 
									AND B.eventid = $eventid
									AND B.type = 'S'
									AND B.eventdate BETWEEN '{$this->fromdate}' AND '{$this->todate}'";
						}
						
						$itemresult = mysql_query($sql);
						
						if ($itemresult) {
							while (($itemmember = mysql_fetch_assoc($itemresult))) {
								$sold = $itemmember['amount'];
							}
							
						} else {
							logError($sql . " - " . mysql_error());
						}
						
						if ($eventid == 0) {
							$sql = "SELECT 
									IFNULL(SUM(B.amount), 0) AS amount 
									FROM {$_SESSION['DB_PREFIX']}eventtransaction B 
									WHERE B.productid = $productid 
									AND B.type = 'B'
									AND B.eventdate BETWEEN '{$this->fromdate}' AND '{$this->todate}'";

						} else {
							$sql = "SELECT 
									IFNULL(SUM(B.amount), 0) AS amount 
									FROM {$_SESSION['DB_PREFIX']}eventtransaction B 
									WHERE B.productid = $productid 
									AND B.eventid = $eventid
									AND B.type = 'B'
									AND B.eventdate BETWEEN '{$this->fromdate}' AND '{$this->todate}'";
						}
						
						$itemresult = mysql_query($sql);
						
						if ($itemresult) {
							while (($itemmember = mysql_fetch_assoc($itemresult))) {
								$broken = $itemmember['amount'];
							}
							
						} else {
							logError($sql . " - " . mysql_error());
						}
						
						if ($eventid == 0) {
							$sql = "SELECT 
									IFNULL(SUM(B.amount), 0) AS amount 
									FROM {$_SESSION['DB_PREFIX']}eventtransaction B 
									WHERE B.productid = $productid 
									AND B.type = 'G'
									AND B.eventdate BETWEEN '{$this->fromdate}' AND '{$this->todate}'";
							
						} else {
							$sql = "SELECT 
									IFNULL(SUM(B.amount), 0) AS amount 
									FROM {$_SESSION['DB_PREFIX']}eventtransaction B 
									WHERE B.productid = $productid 
									AND B.eventid = $eventid
									AND B.type = 'G'
									AND B.eventdate BETWEEN '{$this->fromdate}' AND '{$this->todate}'";
						}
						
						$itemresult = mysql_query($sql);
						
						if ($itemresult) {
							while (($itemmember = mysql_fetch_assoc($itemresult))) {
								$demo = $itemmember['amount'];
							}
							
						} else {
							logError($sql . " - " . mysql_error());
						}
						
						$total[0] += $sold;
						$total[1] += $broken;
						$total[2] += $demo;
						$total[3] += $stock;
						$total[4] += ($sold * $retailprice);
						
						$line = array(
								GetEventName($_POST['eventid']) => $productname,
								"SOLD" => $sold,
								"BROKEN" => $broken,
								"DEMO" => $demo,
								"BALANCE" => number_format($stock, 0),
								"SALES" => number_format($sold * $retailprice, 2)
							);
							
						$this->addLine( $this->GetY(), $line );
					}
					
				} else {
					logError($sql . " - " . mysql_error());
				}
				
				$line = array(
						GetEventName($_POST['eventid']) => "Total",
						"SOLD" => " " . $total[0],
						"BROKEN" => " " . $total[1],
						"DEMO" => " " . $total[2],
						"BALANCE" => " " . $total[3],
						"SALES" => " " . number_format($total[4], 2)
					);
					
				$this->addLine( $this->GetY() + 2, $line );
					
			} catch (Exception $e) {
				logError($e->getMessage());
			}
		}
	}
	
	start_db();
	
	$pdf = new SalesReport( 'P', 'mm', 'A4', $_POST['datefrom'], $_POST['dateto']);
	$pdf->Output();
?>