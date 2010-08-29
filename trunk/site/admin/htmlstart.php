<?php
    session_start();
    include_once("config.php");
    
    header("Content-type: text/html; charset=UTF-8");
    
    if (!isLoggedIn() or !isAdminLogged() ) {
        include ("unallowed.html");
        die();
    } 
    $REMOTE_USER = getAdminName();
    
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
		"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $PAGETITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="../style/admin.css" />
    <link rel="icon" type="image/png" href="../images/site/favicon.png" />
   	<!-- tinyMCE 
		<script language="javascript" type="text/javascript" src="../scripts/tinymce.js"></script>
		<script language="javascript" type="text/javascript">
   		tinyMCE.init({
      		mode : "specific_textareas",
      		auto_reset_designmode : true
   		});
		</script>
		/tinyMCE -->
    <script type="text/javascript" src="../<?php echo DIRECTORY_JSCALENDAR; ?>/calendar.js"></script>
    <script type="text/javascript" src="../<?php echo DIRECTORY_JSCALENDAR; ?>/lang/calendar-en.js"></script>
    <script type="text/javascript" src="../<?php echo DIRECTORY_JSCALENDAR; ?>/calendar-setup.js"></script>
    <link rel="stylesheet" type="text/css" media="screen" href="../<?php echo DIRECTORY_JSCALENDAR; ?>/calendar-blue.css">

</head>
<body>
<h3>

<?php
    echo $PAGETITLE;
    if ($_SERVER["PHP_SELF"] != "/admin/" and $_SERVER["PHP_SELF"] != "/admin/index.php") {
        echo "&nbsp;(<a href=\"/admin\">Menu</a>)";
    }

?>

</h3>
