<?php
	require_once("system-mobileheader.php");
?>
	<style>
		thead td {
			font-weight:bold;
		}
		td {
			font-size:10px;
			height:19px;
		}
	</style>
<?php 
	if (isUserInRole("ADMIN")) {
?>
		<label>Staff Member</label>
<?php 		
		createUserCombo("memberid");
	}
?>
	<table width='100%' cellspacing=5>
		<thead>
			<tr>
				<td>Client</td>
				<td width='46px' align='center'>Due Start</td>
				<td width='46px' align='center'>Due End</td>
				<td width='52px' align='center'>Clock In</td>
				<td width='52px' align='center'>Clock Out</td>
			</tr>
		</thead>
<?php 
	if (isset($_GET['id'])) {
		$memberid = $_GET['id'];
		
	} else {
		$memberid = getLoggedOnMemberID();
	}
	$date = date("Y-m-d");
	$enddate = date ("Y-m-d", strtotime("+7 day", strtotime($date)));
	
	while (strtotime($date) < strtotime($enddate)) {
		$showdate = date ("D d M Y", strtotime($date));
		
		echo "<tr>";
		echo "<td colspan=5>";
        echo "<div>Date : " . $showdate . "</div><br/>";
		echo "</td>";
		echo "</tr>";
	
    	$sql = "SELECT A.id, A.status, 
    			DATE_FORMAT(A.starttime, '%D %d %M %Y') AS startday,
    			DATE_FORMAT(A.starttime, '%H:%i') AS starttime,
    			DATE_FORMAT(A.endtime, '%H:%i') AS endtime,
    			DATE_FORMAT(A.actualstarttime, '%H:%i') AS actualstarttime,
    			DATE_FORMAT(A.actualendtime, '%H:%i') AS actualendtime,
    			B.name, B.id AS clientid
    			FROM {$_SESSION['DB_PREFIX']}diary A 
    			INNER JOIN {$_SESSION['DB_PREFIX']}client B 
    			ON B.id = A.clientid
    			WHERE A.memberid = $memberid
    			AND A.starttime >= '$date' 
    			AND A.endtime <= '$date 23:59:59'
    			AND A.deleted != 'Y'
    			ORDER BY A.starttime";
    	$result = mysql_query($sql);
    	$name = "Unknown";

    	//Check whether the query was successful or not
    	if($result) {
    		while (($member = mysql_fetch_assoc($result))) {
    ?>
    		<tr>
    			<td>
    <?php
    			echo "<a target='_new' href='newstarterreport.php?id=" . $member['clientid'] . "'>" . $member['name'] . "</a>";
    ?>
    			</td>
    			<td align='center'><?php echo $member['starttime']; ?></td>
    			<td align='center'><?php echo $member['endtime']; ?></td>
    			<td width='50px' align='center' id='<?php echo $member['id']; ?>_checkin'>
    <?php 
    			if ($member['status'] == "U") {
    ?>
    				<button style="width:50px; text-align:center" onclick="clockIn(<?php echo $member['id']; ?>)">In</button>
    <?php 
    			} else {
    				echo $member['actualstarttime'];
    			}
    ?>
    			</td>
    			<td width='50px' align='center' id='<?php echo $member['id']; ?>_checkout'>
    <?php 
    			if ($member['status'] == "U") {
    				echo "&nbsp;";
    				
    			} else if ($member['status'] == "I") {
    ?>
    				<button style="width:50px; text-align:center" onclick="clockOut(<?php echo $member['id']; ?>)">Out</button>
    <?php 
    			} else {
    				echo $member['actualendtime'];
    			}
    ?>
    			</td>
    		</tr>
    <?php
    		}
    		
    	} else {
    		logError($sql . " " . mysql_error());
    	}
    	
		echo "<tr>";
		echo "<td colspan=5>";
        echo "<hr />";
		echo "</td>";
		echo "</tr>";
		
		$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
	}
?>
	</table>
	<script>
		$(document).ready(
				function() {
<?php 
	if (isset($_GET['id'])) {
?>
					$("#memberid").val("<?php echo $_GET['id']; ?>");
<?php 
	}
?>
					$("#memberid").change(
							function() {
								window.location.href = "myplanner.php?id=" + $(this).val();
							}
						);
				}
			);
		
		function clockIn(id) {
			callAjax(
					"checkin.php", 
					{ 
						id: id
					},
					function(data) {
						$("#" + id + "_checkin").html(data.checkin);
						$("#" + id + "_checkout").html("<button style='width:50px; text-align:center' onclick='clockOut(" + id + ")'>Out</button>");
					},
					false
				);
		}

		function clockOut(id) {
			callAjax(
					"checkout.php", 
					{ 
						id: id
					},
					function(data) {
						$("#" + id + "_checkout").html(data.checkout);
					},
					false
				);
		}
	</script>
<?php 
	require_once("system-mobilefooter.php");
?>	
	