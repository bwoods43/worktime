<?php $this->get('header'); ?>

<?php echo $this->render('/admin/sidebar'); ?>

<div id="work_area">

	<h2>Edit Task <q><?php echo htmlentities($this->task->task_name); ?></q></h2>

	<?php $this->get('messaging'); ?>

	<p><a href="/admin/tasks">&laquo; Back to all tasks</a></p>

	<form method="post" action="" class="basic_form">
		<p>
			<label for="task[task_name]">
				Task Name:
			</label>
			<input type="text" name="task[task_name]" id="task[task_name]" tabindex="1" class="text" value="<?php echo htmlentities($this->clean['task']['task_name']); ?>" size="60" />
		</p>
		<p>
			<input type="submit" value="Save Task" class="button" />
		</p>
	</form>

</div>

<?php $this->get('footer'); ?>