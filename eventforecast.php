<?php
	require_once("system-header.php");
?>
<link rel="stylesheet" href="css/fullcalendar.css" type="text/css" media="all" />
<link rel="stylesheet" href="css/fullcalendar.print.css" type="text/css" media="all" />
<script type="text/javascript" src="js/fullcalendar.min.js"></script>
<h5>Event</h5>

<div id='calendar'></div>
<div id="detaildialog" class="modal">
	<table cellspacing=5>
		<tr>
			<td>Forecast</td>
			<td>
				<input size=7 type="text" id="amount" />
			</td>
		</tr>
		<tr>
			<td>Date</td>
			<td>
				<input type="text" id="forecastdate" class="datepicker" />
			</td>
		</tr>
	</table>
</div>
<script>
	var currentID = null;
	
	$(document).ready(
			function() {
				$("#detaildialog").dialog({
						modal: true,
						width: 300,
						autoOpen: false,
						title: "Forecast",
						buttons: {
							Ok: function() {
								callAjax(
										"forecastsave.php", 
										{ 
											id: currentID,
											amount: $("#amount").val(),
											eventid: <?php echo $_GET['id']; ?>,
											date: $("#forecastdate").val()
										},
										function(data) {
											$("#calendar").fullCalendar("refetchEvents");
										}
									);
								
								$(this).dialog("close");
							},
							"Delete": function() {
								callAjax(
										"forecastdelete.php", 
										{ 
											id: currentID
										},
										function(data) {
											$("#calendar").fullCalendar("refetchEvents");
										}
									);
								
								$(this).dialog("close");
							},
							Cancel: function() {
								$(this).dialog("close");
							}
						}
					});
								
				$('#calendar').fullCalendar({
					editable: true,
					aspectRatio: 2.1,
					allDayDefault: false, 
					
					header: {
						left: 'prev,next today',
						center: 'title',
						right: 'month,agendaWeek,agendaDay'
					},

					eventRender: function(event, element) {
					   element.attr('title', "Click to view " + event.title);
					},
					
					eventClick: function(calEvent, jsEvent, view) {
						if (calEvent.id != 0) {
							callAjax(
									"finddata.php", 
									{ 
										sql: "SELECT A.amount, DATE_FORMAT(A.forecastdate, '%d/%m/%Y') AS forecastdate " +
											 "FROM <?php echo $_SESSION['DB_PREFIX'];?>eventforecast A " + 
											 "WHERE A.id = " + calEvent.id
									},
									function(data) {
										if (data.length > 0) {
											var node = data[0];

											currentID = calEvent.id;

											$("#amount").val(node.amount);
											$("#forecastdate").val(node.forecastdate);
											$("#detaildialog").dialog("open");
											
										}
									}
								);
						}
				    },
					
				    dayClick: function(date, allDay, jsEvent, view) {
						currentID = 0;

						$("#amount").val("");
						$("#forecastdate").val(dateToDMY(date));
						$("#detaildialog").dialog("open");
				    },
				    
				    events: function(start, end, callback) {
				    	var startYear = start.getYear();
				    	var endYear = end.getYear();
				    	
				    	if (startYear < 2000) {
				    	    startYear += 1900;
				    	}
				    	
				    	if (endYear < 2000) {
				    	    endYear += 1900;
				    	}
				    	
					    $.ajax({
			                type: 'POST',
			                url: 'forecastdata.php',
			                async: false,
			                dataType:'json',
					        data: {
					        	eventid: <?php echo $_GET['id']; ?>,
					            start: startYear + "-" + padZero(start.getMonth() + 1) + "-" + padZero(start.getDate()),
			                    end: endYear + "-" + padZero(end.getMonth() + 1) + "-" + padZero(end.getDate()),      
					        },
					        error: function(error) {
					            alert('there was an error while fetching events');
					        },
					        success: function(msg) {
								var events = [];
								 
		                        for(var c = 0; c < msg.length; c++){
		                        	var item = msg[c];
		                        	
		                            events.push({
			                                id: item.id,                                
			                                title: item.title,
			                                allDay: item.allDay == "true" ? true : false,
			                                start: item.start,
			                                end: item.end,
			                                editable: true,
			                                className: item.className
			                            });
		                        }
		                        
		                        callback(events);
					        }
					     });
				    }
				});
			}
		);
</script>

<?php
	require_once("system-footer.php");
?>
