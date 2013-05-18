<?php $this->get('header'); ?>

<?php echo $this->render('/admin/sidebar'); ?>

<div id="work_area">

<?php if ( $this->success ) : ?>

	<h2><q><?php echo htmlentities($this->task->task_name); ?></q> Deleted</h2>
	
	<?php $this->get('messaging'); ?>
	
	<p><a href="/admin/tasks">&laquo; Back to all tasks</a></p>
	
<?php else: ?>

	<h2>Delete Task : <q><?php echo htmlentities($this->task->task_name); ?></q></h2>

	<?php $this->get('messaging'); ?>

	<p><a href="/admin/tasks">&laquo; Back to all tasks</a></p>

	<form method="post" action="" class="basic_form">
		<h3>Are you sure?</h3>
		<p class="errors">
			Deletinge this task will remove it from any and all projects as well
			as delete any and all time entries for this task. 
		</p>
		<p>
			Please make sure this is what you'd like to do before confirming below.
		</p>
		<p>
			<input type="hidden" name="delete[ID]" value="<?php echo $this->task->ID; ?>" />
			<input type="submit" value="Confirm Deletion" class="button" />
		</p>
	</form>

<?php endif;?>

</div>

<?php $this->get('footer'); ?>