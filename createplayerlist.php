<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$agegroupid = $_POST['agegroupid'];
	
	if (isset($_POST['selected'])) {
		$selected = json_decode($_POST['selected']);
		
	} else {
		$selected = array();
	}
	
	$sql = "SELECT * 
			FROM {$_SESSION['DB_PREFIX']}player 
			WHERE agegroupid = $agegroupid
			ORDER BY firstname, lastname";
	$result = mysql_query($sql);
	
	//Check whether the query was successful or not
	if($result) {
		while($member = mysql_fetch_assoc($result)) {
			$name = $member['firstname'] . " " . $member['lastname'];
			$id = $member['id'];
			
			if (in_array($id, $selected)) {
				$checked = " checked";

			} else {
				$checked = "";
			}
?>
	<div class="checkboxcontainer">
		<input type="checkbox" name="player[]" value="<?php echo $id; ?>" <?php echo $checked; ?>><?php echo $name; ?></input>
	</div>
<?php 
		}
		
	} else {
		logError("$sql -" . mysql_error());
	}

?>