<?php $this->get('header'); ?>

<?php echo $this->render('/admin/sidebar'); ?>

<div id="work_area">


<?php if ( $this->success ) :  ?>
	<h2><?php echo htmlentities($this->user->first_name .' ' . $this->user->last_name); ?> Deleted.</h2>
	<?php echo $this->get('messaging');?>
<?php else: ?>

	<h2>Delete <?php echo htmlentities($this->user->first_name .' ' . $this->user->last_name); ?>?</h2>
	
	<form method="post" action="" class="basic_form">
		<h3>Are You Sure?</h3>
		<p class="errors">
			<strong>This will delete <?php echo htmlentities($this->user->first_name .' ' . $this->user->last_name); ?>'s 
			account, time records and will remove them from any projects.</strong> 
		</p>
		<p>
			<input type="hidden" name="delete[ID]" value="<?php echo $this->user->ID; ?>" />
			<input type="submit" value="Confirm Deletion" class="button" tabindex="1" />
		</p>
	</form>
	
<?php endif; ?>
	
</div>

<?php $this->get('footer'); ?>