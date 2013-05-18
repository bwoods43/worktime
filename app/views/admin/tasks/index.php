<?php $this->get('header'); ?>

<?php echo $this->render('/admin/sidebar'); ?>

<div id="work_area">

	<h2>Tasks</h2>
	<?php if ( count($this->tasks) > 0 ) : ?>
	<p>
		Manage your existing tasks or <a href="/admin/tasks/add">add a new task</a>.
	</p>
	
	<table class="listing">
	<thead>
	<tr>
		<th>Task Name</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ( $this->tasks as $task ) : ?>
	<tr>
		<td>
			<a href="/admin/tasks/edit/<?php echo $task->ID; ?>" class="edit">
				<?php echo htmlentities($task->task_name); ?>
			</a>
			<div class="row-actions">
				<span>
					<a href="/admin/tasks/edit/<?php echo $task->ID; ?>" class="edit">Edit</a> | 
<?php
// 02/09/13 baw removing task deletion functionality
/*

					<a href="/admin/tasks/delete/<?php echo $task->ID; ?>" class="delete">Delete</a>
*/
?>
				</span>
			</div>
		</td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
	
	<?php else: ?>
	
	<p>
		You have no tasks in your system, <a href="/admin/tasks/add">add a new task</a>.
	</p>
	
	<?php endif; ?>

</div>

<?php $this->get('footer'); ?>