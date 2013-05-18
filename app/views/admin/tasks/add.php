<?php $this->get('header'); ?>

<?php echo $this->render('/admin/sidebar'); ?>

<div id="work_area">

	<h2>Add Task</h2>

	<?php $this->get('messaging'); ?>

	<form method="post" action="" class="basic_form">
		
		<p>
			<label for="task[task_name]">
				Task Name:
			</label>
			<input type="text" name="task[task_name]" id="task[task_name]" tabindex="1" class="text" value="<?php echo htmlentities($this->clean['task']['task_name']); ?>" size="60" />
		</p>
		<p>
			<input type="submit" value="Add Task" class="button" />
		</p>
	
	</form>

</div>

<?php $this->get('footer'); ?>