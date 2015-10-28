<?php 
	include("system-header.php");
	
	if (isset($_GET['mode'])) {
		$mode = $_GET['mode'];
		
	} else {
		$mode = "0";
	}
	?>
	<meta http-equiv="expires" content="Sun, 01 Jan 2014 00:00:00 GMT"/>
	<meta http-equiv="pragma" content="no-cache" />
	<script src='./codebase/dhtmlxscheduler.js' type="text/javascript" charset="utf-8"></script>
	<script src='./codebase/ext/dhtmlxscheduler_timeline.js' type="text/javascript" charset="utf-8"></script>
	<script src='js/jquery.ui.timepicker.js'></script>
	<link rel='STYLESHEET' type='text/css' href='./codebase/dhtmlxscheduler_glossy.css'>
	<link rel="stylesheet" href="./codebase/ext/dhtmlxscheduler_ext.css" type="text/css" media="screen" title="no title" charset="utf-8">
	
	<style type="text/css" media="screen">
		.dhx_cal_event_line  {
			font-size:18px ! important;
			border:0px solid black ! important;
			line-height:15px ! important;
			height:100% ! important;
		}
		.dhx_event_resize  {
			display: none;
		}
		.entry {
			display: block;
		}
		.toleft {
			display: inline-block;
			width:60px;
		}
		.toright {
			display: inline-block;
			float:right;
			width:71px;
			font-size:8px;
			padding-right: 2px;
			padding-left: 2px;
			background-color: white;
		}
		.keyblock {
			width:10px;
			height:10px;
			border:1px solid black;
		}
		.yellow {
			background-color: yellow;
		}
		.blue {
			background-color: blue;
		}
		.grey {
			background-color: #E0E0E0;
		}
		.purple {
			background-color: purple;
		}
		.broken {
			margin-top: 10px;
			color: red ! important;
		}
		.green {
			color: #55FF55;
		}
		.one_line{
			white-space:nowrap;
			overflow:hidden;
			padding-top:5px; padding-left:5px;
			text-align:left !important;
		}
		
		.dhx_cal_event_line  {
			font-size:11px;
			line-height:12px;
		}
	</style>
	
	<script type="text/javascript" charset="utf-8">
		$(document).ready(
				function() {
					init();
				}
			);

		function init() {
			modSchedHeight();
			
			scheduler.locale.labels.timeline_tab = "Timeline";
			scheduler.locale.labels.section_custom="Section";
			scheduler.config.details_on_create=false;
			scheduler.config.dblclick_create = false;
			scheduler.config.drag_in = false;	      	
			
			scheduler.attachEvent("onBeforeViewChange", function (old_mode, old_date, mode, date) {
			    if (old_mode != mode || +old_date != +date)
			        scheduler.clearAll();
			    return true;
			});
			scheduler.attachEvent("onBeforeDrag",function(){return false;})
	      	scheduler.attachEvent("onDblClick",function(){return false;})
	      	scheduler.attachEvent("onClick",function(){return false;})
			scheduler.config.xml_date="%Y-%m-%d %H:%i";
			scheduler.attachEvent("onBeforeFolderToggle", function(section,isOpen,allSections){
			    //any custom logic here
			    return false;
			});			
			scheduler.config.first_hour = 6;
			scheduler.config.last_hour = 23;
			//===============
			//Configuration
			//===============
			var sections=[
<?php 
				$sql = "SELECT id, name FROM {$_SESSION['DB_PREFIX']}product ORDER BY name";
				
				$result = mysql_query($sql);
				$first = true;
			
				//Check whether the query was successful or not
				if($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($first) {
							$first = false;
						} else {
							echo ", ";
						}
?>
						{key:<?php echo $member['id']; ?>, label:"<?php echo $member['name']; ?>"}
<?php
					}
				}
		
?>
			];
			
<?php 
				$sql = "SELECT count(*) AS amount FROM {$_SESSION['DB_PREFIX']}event";
				$result = mysql_query($sql);
				$count = 10;
			
				//Check whether the query was successful or not
				if($result) {
					while (($member = mysql_fetch_assoc($result))) {
						$count = 100 / $member['amount'];
					}
				} else {
					logError($sql . " " . mysql_error());
				}
				
				$sql = "SELECT id, shortname FROM {$_SESSION['DB_PREFIX']}event ORDER BY shortname";
				$html = "<DIV style='padding-left:12px'><TABLE cellspacing=0 cellpadding=0 border=1 width=100%><TR>";
				
				$result = mysql_query($sql);
				$first = true;
			
				//Check whether the query was successful or not
				if($result) {
					while (($member = mysql_fetch_assoc($result))) {
						$html .= "<TD style='font-size:9px' width='" . number_format($count, 2) . "%'>" . $member['shortname'] . "</TD>";
					}
				}
				
				$html .= "</TR></TABLE></DIV>";
?>
				
			scheduler.createTimelineView({
				name:	"timeline",
				 x_unit:"day",//measuring unit of the X-Axis.
			     x_date:"<?php echo $html; ?>", //date format of the X-Axis
			     x_step:1,      //X-Axis step in 'x_unit's
			     x_size:1,      //X-Axis length specified as the total number of 'x_step's
			     x_start:0,     //X-Axis offset in 'x_unit's
			     x_length:1,    //number of 'x_step's that will be scrolled at a time
			     dy: 80,
				y_unit:	sections,
				y_property:	"section_id",
				render:"bar",
				sort:function(a, b){
				    var val = a.true_start_date > b.true_start_date ? 1 : -1;

				    return val;
                }
			});
				
			//===============
			//Data loading
			//===============
			scheduler.config.lightbox.sections=[	
				{name:"description", height:130, map_to:"text", type:"textarea" , focus:true},
				{name:"custom", height:23, type:"select", options:sections, map_to:"section_id" },
				{name:"time", height:12, type:"time", map_to:"auto"}
			];
<?php 
			$date = new DateTime(date("Y-m-d"));
?>			

			scheduler.init('scheduler_here',new Date("<?php echo $date->format('Y-m-d'); ?>"),"timeline");
			scheduler.setLoadMode("day");
			scheduler.config.show_loading = true;
			
			scheduler.load("clientevents.php?mode=<?php echo $mode; ?>&ts=<?php echo time(); ?>","json",function(){
			
			    // alert("Data has been successfully loaded");
			    scheduler.updateCollection("sections",sections );
			

			    setTimeout(
					    function() {
						    refresh();
					    },
					    <?php echo getSiteConfigData()->refreshcycle * 1000; ?>
					);

			});
			var dp = new dataProcessor("clientevents.php");
			dp.init(scheduler);
			
							

		}

		function refresh() {
			scheduler.clearAll();
			scheduler.setCurrentView(null, "timeline");
			
		    setTimeout(
				    function() {
					    refresh();
				    },
				    <?php echo getSiteConfigData()->refreshcycle * 1000; ?>
				);
		}
		
		function modSchedHeight(){
			var sch = document.getElementById("scheduler_here");
			sch.style.height = (document.body.offsetHeight - 20) + "px";
			var contbox = document.getElementById("contbox");
			contbox.style.width = (parseInt(document.body.offsetWidth)-300)+"px";
		}
	</script>
	<div style="height:0px;background-color:#3D3D3D;border-bottom:5px solid #828282;">
		<div id="contbox" style="float:left;color:white;margin:22px 75px 0 75px; overflow:hidden;font: 17px Arial,Helvetica;color:white">
		</div>
	</div>
	<!-- end. info block -->
	<div id="scheduler_here" class="dhx_cal_container" style='width:100%; height:100%;'>
		<div class="dhx_cal_navline">
			<div class="dhx_cal_prev_button">&nbsp;</div>
			<div class="dhx_cal_next_button">&nbsp;</div>
			<div class="dhx_cal_date"></div>
			<div class="dhx_cal_tab" name="timeline_tab" style="right:280px;"></div>
		</div>
		<div class="dhx_cal_header">
		</div>
		<div class="dhx_cal_data">
		</div>		
	</div>
<?php 
	include("system-footer.php");
?>