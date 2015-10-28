<?php
	require_once("system-header.php");
?>
<div class="basicform">
	<h2>Report Revenue By Site</h2>
	<br>
	<form id="reportform" class="reportform" name="reportform" method="POST" action="reportrevenuebysitelib.php" target="_new">
		<table>
			<tr>
				<td>
					Date From
				</td>
				<td>
					<input class="datepicker" required="true" id="datefrom" name="datefrom" />
				</td>
			</tr>
			<tr>
				<td>
					Event
				</td>
				<td>
					<?php createCombo("eventid", "id", "name", "{$_SESSION['DB_PREFIX']}event", "", false); ?>
				</td>
			</tr>
			<tr>
				<td>
					&nbsp;
				</td>
				<td>
					<a class="link1" href="javascript: runreport();"><em><b>Run Report</b></em></a>
				</td>
			</tr>
		</table>
	</form>
</div>

<script>
	function runreport(e) {
		if (! verifyStandardForm("#reportform")) {
			return false;
		}
		
		$('#reportform').submit();

		try {
			e.preventDefault();

		} catch (e) {

		}
	}
</script>
<?php
	require_once("system-footer.php");
?>
