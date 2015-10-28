<?php 
	require_once("system-mobileheader.php"); 
?>
	<form action="system-client-exec.php?ts=<?php echo time(); ?>" method="POST" id="loginForm">
		<br>
		<div><label>Event</label></div>
		<br>
		<?php createCombo("eventid", "id", "name", "{$_SESSION['DB_PREFIX']}event")?>
		<br>
		<br>
		<input type="submit" value="Confirm"></input>
	</form>
	<script>
		$(document).ready(
				function() {
				}
			);
	</script>
</div>

<?php include("system-mobilefooter.php"); ?>					
