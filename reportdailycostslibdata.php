<?php
	require_once('system-db.php');
	require_once('pdfreport.php');
	require_once("simple_html_dom.php");
	
	class SalesReport extends PDFReport {
		private $dateFrom;
		
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
			
			$size = $this->addText( 10, 5, "Report Daily Takings", 12, 4, 'B');
			$size = $this->addText( 10, 14, "Date : " . $this->dateFrom, 10, 4, 'B') + 5;
			
			$this->SetFont('Arial','', 12);
				
			$cols = array( 
					"Event"  => 106,
					"Takings"  => 42,
					"Expected"  => 42
			);
			
			$this->addCols($size + 2, $cols);

			$cols = array(
					"Event"  => "L",
					"Takings"  => "R",
					"Expected" => "R"
				);
			$this->addLineFormat( $cols);
			$this->SetY(32);
		}
		
		function __construct($orientation, $metric, $size, $datefrom) {
			$dynamicY = 0;
			
			$this->dateFrom = $datefrom;
			
			start_db();
			
	        parent::__construct($orientation, $metric, $size);
	        
	        $this->SetAutoPageBreak(true, 30);
			$this->AddPage();
			
			try {
				$startdate = convertStringToDate($this->dateFrom);
				
				$sql = "SELECT A.name, C.amount,
					    (
					   		SELECT SUM(B.amount) * D.retailprice
					   		FROM {$_SESSION['DB_PREFIX']}eventtransaction B 
					   		INNER JOIN {$_SESSION['DB_PREFIX']}product D
					   		ON D.id = B.productid 
					   		WHERE B.eventid = A.id 
					   		AND B.eventdate = '$startdate' 
					   		AND B.type = 'S'
					    ) AS sold
					    FROM {$_SESSION['DB_PREFIX']}event A 
					    LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}eventforecast C
					    ON C.eventid = A.id
					    AND C.forecastdate = '$startdate' 
						ORDER BY A.name";
				
				$result = mysql_query($sql);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						$sold = $member['sold'] != "" ? $member['sold'] : 0;
						$line=array(
								"Event"  => $member['name'],
								"Takings"  => "£ " . number_format($sold, 2),
								"Expected" => "£ " . number_format($member['amount'], 2)
							);
							
						$this->addLine( $this->GetY(), $line, 6.2);
					}
					
				} else {
					logError($sql . " - " . mysql_error());
				}
				
			} catch (Exception $e) {
				logError($e->getMessage());
			}
		}
	}
?>