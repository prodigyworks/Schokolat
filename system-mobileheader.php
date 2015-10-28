<?php
	require_once('system-db.php');
	
	if(!isset($_SESSION)) {
		session_start();
	}
	
	if (! isAuthenticated() && ! endsWith($_SERVER['PHP_SELF'], "system-login.php")) {
		header("location: m.system-login.php?session=" . urlencode(base64_encode($_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] )));
		exit();
	}
	
	if (! isset($_SESSION['SESS_EVENT_ID']) && (! endsWith($_SERVER['PHP_SELF'], "system-client.php") && ! endsWith($_SERVER['PHP_SELF'], "system-login.php"))) {
		header("location: system-client.php");
		exit();
	}
	
?>
<?php 
	//Include database connection details
	require_once('system-config.php');
	require_once("confirmdialog.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Schokolat</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<link rel="shortcut icon" href="favicon.ico">

<link href="css/m.style.css?a=3" rel="stylesheet" type="text/css" />
<!-- 
<link href="css/jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css" />
 -->
<link href="css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="css/dcmegamenu.css" rel="stylesheet" type="text/css" />
<link href="css/skins/white.css" rel="stylesheet" type="text/css" />


<script src="js/jquery-1.8.0.min.js" type="text/javascript"></script>
<script src="js/jquery.min.js" type="text/javascript"></script>
<script src="js/jquery-ui.min.js" type="text/javascript"></script>
<script src='js/jquery.hoverIntent.minified.js' type='text/javascript'></script>
<script src='js/jquery.dcmegamenu.1.3.3.js' type='text/javascript'></script>
<script src="js/oraclelogs.js" language="javascript" ></script>
<!--[if lt IE 7]>
<script type="text/javascript" src="js/ie_png.js"></script>
<script type="text/javascript">
	ie_png.fix('.png, .carousel-box .next img, .carousel-box .prev img');
</script>
<link href="css/ie6.css" rel="stylesheet" type="text/css" />
<![endif]-->
</head>
<body>
		<div id="embeddedcontent">
			<div class="embeddedpage">
				<div class="title"><?php echo $_SESSION['title']; ?></div>
				<div class="logout">
					<a href="system-logout.php">Log Out</a>
				</div>
				<div>&nbsp;</div>
				<div>&nbsp;</div>
				<hr>
				<div>&nbsp;</div>

			