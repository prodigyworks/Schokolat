<?php
	require_once('system-db.php');
	require_once('pdfreport.php');
	require_once("simple_html_dom.php");
	
	class SalesReport extends PDFReport {
		private $fromdate;
		private $todate;
		private $dates;
		
		function AddPage($orientation='', $size='') {
			parent::AddPage($orientation, $size);
			
			$this->Image("images/logomain2.png", 132.6, 1);
			
			$size = $this->addText( 10, 5, "Report Revenue By Site", 12, 4, 'B');
			
			$size = $this->addText( 10, 14, "Event : " . GetEventName($_POST['eventid']), 10, 4, 'B') + 5;
			$size = $this->addText( 10, 19, "Between : " . date("d-M-Y", strtotime($this->fromdate)) . " and " . date ("d-M-Y", strtotime($this->todate)), 10, 4, 'B') + 5;
			
			$this->SetFont('Arial','', 9);
			
			$this->dates = array();
			$date = $this->fromdate;
			
			while (strtotime($date) <= strtotime($this->todate)) {
				array_push($this->dates, date ("d-M-Y", strtotime($date)));
				
				$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
			}
			
			$cols = array(
					GetEventName($_POST['eventid']) => 43,
					$this->dates[0] => 21,
					$this->dates[1] => 21,
					$this->dates[2] => 21,
					$this->dates[3] => 21,
					$this->dates[4] => 21,
					$this->dates[5] => 21,
					$this->dates[6] => 21
				);
				
			$this->addCols($size, $cols);

			$cols = array(
					GetEventName($_POST['eventid']) => "L",
					$this->dates[0] => "R",
					$this->dates[1] => "R",
					$this->dates[2] => "R",
					$this->dates[3] => "R",
					$this->dates[4] => "R",
					$this->dates[5] => "R",
					$this->dates[6] => "R"
				);
				
			$this->addLineFormat( $cols);
			$this->SetY(36);
		}
		
		function __construct($orientation, $metric, $size, $startdate) {
			$this->fromdate = convertStringToDate($startdate);
			$this->todate = date ("Y-m-d", strtotime("+1 week", strtotime($this->fromdate)));
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
				$total[5] = 0;
				$total[6] = 0;
				
				$sql = "SELECT A.id, A.name, A.retailprice
						FROM {$_SESSION['DB_PREFIX']}product A 
						ORDER BY A.name";
				
				$result = mysql_query($sql);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						$productid = $member['id'];
						$productname = $member['name'];
						$retailprice = $member['retailprice'];
						
						$date = $this->fromdate;
						$amounts = array();
						
						while (strtotime($date) <= strtotime($this->todate)) {
							if ($eventid == 0) {
								$sql = "SELECT IFNULL(SUM(B.amount), 0) AS amount
										FROM {$_SESSION['DB_PREFIX']}eventtransaction B 
										WHERE B.productid = $productid 
										AND B.eventdate = '$date'";
								
							} else {
								$sql = "SELECT IFNULL(SUM(B.amount), 0) AS amount
										FROM {$_SESSION['DB_PREFIX']}eventtransaction B 
										WHERE B.productid = $productid 
										AND B.eventid = $eventid
										AND B.eventdate = '$date'";
							}
							
							$itemresult = mysql_query($sql);
							
							if ($itemresult) {
								while (($itemmember = mysql_fetch_assoc($itemresult))) {
									array_push($amounts, $itemmember['amount'] * $retailprice);
								}
								
							} else {
								logError($sql . " - " . mysql_error());
							}
							
							$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
						}
						
						$total[0] += $amounts[0];
						$total[1] += $amounts[1];
						$total[2] += $amounts[2];
						$total[3] += $amounts[3];
						$total[4] += $amounts[4];
						$total[5] += $amounts[5];
						$total[6] += $amounts[6];
						
						$line = array(
								GetEventName($_POST['eventid']) => $productname,
								$this->dates[0] => number_format($amounts[0], 2),
								$this->dates[1] => number_format($amounts[1], 2),
								$this->dates[2] => number_format($amounts[2], 2),
								$this->dates[3] => number_format($amounts[3], 2),
								$this->dates[4] => number_format($amounts[4], 2),
								$this->dates[5] => number_format($amounts[5], 2),
								$this->dates[6] => number_format($amounts[6], 2)
							);
							
						$this->addLine( $this->GetY(), $line, 5);
					}
					
				} else {
					logError($sql . " - " . mysql_error());
				}
				
				$line = array(
						GetEventName($_POST['eventid']) => "Total",
						$this->dates[0] => " " . number_format($total[0], 2),
						$this->dates[1] => " " . number_format($total[1], 2),
						$this->dates[2] => " " . number_format($total[2], 2),
						$this->dates[3] => " " . number_format($total[3], 2),
						$this->dates[4] => " " . number_format($total[4], 2),
						$this->dates[5] => " " . number_format($total[5], 2),
						$this->dates[6] => " " . number_format($total[6], 2)
					);
					
				$this->addLine( $this->GetY() + 2, $line, 5 );
					
			} catch (Exception $e) {
				logError($e->getMessage());
			}
		}
	}
	
	start_db();
	
	$pdf = new SalesReport( 'P', 'mm', 'A4', $_POST['datefrom']);
	$pdf->Output();
?>