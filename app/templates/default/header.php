<?php
// 3/4/11 baw - array for people who can switch offices
$switch_offices = array(1,10,15,16,64);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo $this->page_title; ?></title>
	<link rel="stylesheet" type="text/css" href="/style.css" media="all" />
	<link rel="stylesheet" type="text/css" href="/print.css" media="print" />
	<link rel="stylesheet" type="text/css" href="/jquery-ui.css" />
	<?php $this->get('javascript'); ?>
	
</head>
<body>

<div id="header"><div class="wrap">
	<h1><a href="/">Benyon Time</a></h1>
	<?php if ( User::isLoggedIn() ) : ?>
	<p>
		<?php if ( User::isAdmin() ) : ?>
		<a href="/">Admin</a> | 
		<?php endif; ?>
		<a href="/settings">Account Settings</a> | 
		<a href="/logout">Logout</a><br />
		Welcome to the <?php print User::getOfficeName($_SESSION['beynon_office_id']); ?> office!
		
		<?php if (User::isAdmin() && in_array($_SESSION['beynon_id'], $switch_offices)) print ' | <a href="/admin?switchoffice=1">Switch offices</a>'; ?>
		
		<?php //echo "<PRE>"; print_r($_SESSION); ?>

	</p>
	<?php endif; ?>
</div></div>

<div id="page">