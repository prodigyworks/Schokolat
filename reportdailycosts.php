<?php
	require_once("system-header.php");
?>
<div class="basicform">
	<h2>Report Sales</h2>
	<br>
	<form id="reportform" class="reportform" name="reportform" method="POST" action="reportdailycostslib.php" target="_new">
		<table>
			<tr>
				<td>
					Date 
				</td>
				<td>
					<input class="datepicker" required="true" id="datefrom" name="datefrom" value="<?php echo date("d/m/Y"); ?>" />
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
