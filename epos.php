<?php 
	require_once("system-mobileheader.php"); 
	
	start_db();
?>
	<style>
		button {
			padding:16px;
			margin:12px;
			font-size:12px;
			width:110px;
		}
		
		.selected {
			background-color: red;
			color: white;
		}
		
		.unselected {
			background-color: grey;
			color: black;
		}
		
		input {
			font-size: 20px;
		}
		
		input[type='submit'] {
//			visibility: hidden;
		}
	</style>
    <div id="loginForm">
<?php 	
	if (isset($_POST['barcode'])) {

		$barcodeid = $_POST['barcode'];
		$type = $_POST['barcodetype'];
		$eventid = getLoggedOnEventID();
		$productid = 0;
		$amount = 1;

		if ($type == "R") {
			$type = "S";
			$amount = -1;
		}
		
		$sql = "SELECT id, name
				FROM {$_SESSION['DB_PREFIX']}product 
				WHERE productid = '$barcodeid'"; 
		$result = mysql_query($sql);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$productid = $member['id'];
				$name = $member['name'];
			}
			
		} else {
			logError($sql . " - " . mysql_error());
		}

		if ($productid == 0) {
			echo "<h3>Invalid Barcode</h3>";
			
		} else {
			$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}eventtransaction 
					(
						eventid, productid, amount, type, eventdate
					)
					VALUES
					(
						$eventid, $productid, $amount, '$type', CURDATE()
					)";
						
			if (! mysql_query($sql)) {
				logError($sql . " - " . mysql_error());
			}
			
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}eventproductmatrix SET
					stock = stock - ($amount)
					WHERE productid = $productid
					AND eventid = $eventid";
						
			if (! mysql_query($sql)) {
				logError($sql . " - " . mysql_error());
			}
			
			if ($_POST['barcodetype'] == "S") {
				echo "<h3>Sold single $name</h3>";
				
			} else if ($_POST['barcodetype'] == "B") {
				echo "<h3>Broken single $name</h3>";
				
			} else if ($_POST['barcodetype'] == "G") {
				echo "<h3>Demo single $name</h3>";
				
			} else if ($_POST['barcodetype'] == "R") {
				echo "<h3>Demo single $name</h3>";
			}
		}
	}
?>
		<br />
		<br />
		<h1>Enter Product</h1>
        <form method="POST" action="epos.php">
            <input type="text" id="barcode" name="barcode" />
			<br />
			<br />
			
			<input type="hidden" id="barcodetype" name="barcodetype" value="S" />
            <input type="submit" style="display:none" />
        </form>        
			
		<button id="btnTypeSale" class="selected">SALE</button>
		<button id="btnTypeBreak" class="unselected">BREAKAGE</button>
		<br>
		<button id="btnTypeGive" class="unselected">DEMO</button>
		<button id="btnTypeRefund" class="unselected">REFUND</button>
    </div>
    </body>
    <script>
    	$(document).ready(
    	    	function() {
        	    	$("#btnTypeSale").click(
                	    	function() {
                            	$("#barcodetype").val("S");
                            	$("#barcode").focus();
                	    	}
                	    );
        	    	$("#btnTypeRefund").click(
                	    	function() {
                            	$("#barcodetype").val("R");
                            	$("#barcode").focus();
                	    	}
                	    );
        	    	$("#btnTypeBreak").click(
                	    	function() {
                            	$("#barcodetype").val("B");
                            	$("#barcode").focus();
                	    	}
                	    );
        	    	$("#btnTypeGive").click(
                	    	function() {
                            	$("#barcodetype").val("G");
                            	$("#barcode").focus();
                	    	}
                	    );

            	    $("button").click(
                    	    function() {
                            	$("button").attr("class", "unselected");
                            	$(this).attr("class", "selected");
                    	    }
                   		);
    	    	}
    	    );
    </script>
<?php 

	require_once("system-mobilefooter.php"); 
?>