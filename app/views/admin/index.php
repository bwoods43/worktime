<?php
/*
switch offices - right now, there are only two offices:
1 - maryland
2- oregon
if we get more, we'll need to build the logic to switch between various offices. for now, we can just use a 'switch office' link and switch to the other potential office
can use User::getOfficeID() to get office id
*/
if ($_GET['switchoffice'] == 1) {
	if ($_SESSION['beynon_office_id'] == 1) {
		$_SESSION['beynon_office_id'] = 2;
	} else {
		$_SESSION['beynon_office_id'] = 1;
	}
	header('Location: /admin');
}
?>

<?php $this->get('header'); ?>

<?php echo $this->render('/admin/sidebar'); ?>

<div id="work_area">

	<h2>Admin Area</h2>
	<p>Please choose an option from the admin menu on the left.</p>
</div>

<?php $this->get('footer'); ?>