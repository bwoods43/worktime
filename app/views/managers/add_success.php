<?php $this->get('header'); ?>

<div id="time_entry">

	<?php echo $this->render('/managers/jumper'); ?>

	<h2>
		<small>Time Entry</small>
		<br />
		<?php echo htmlentities($this->project->project_name); ?> : <?php echo date("F j, Y", $this->date); ?>
	</h2>
	
	<div class="success">Your time entries were saved!</div>
	
	<p>A summary of the recently added time is displayed below. <a href="/">Start over</a> or <a href="/managers/review/<?php echo $this->project->ID; ?>/?date=<?php echo htmlentities(date("m/d/Y", $this->date)); ?>">edit all entries for this day</a>.	</p>
	<p>
	<strong>Select another day for this project:</strong>
	<form method="get" action="/managers/add/<?php echo $this->project->ID; ?>/" class="basic_form">
		<input type="text" name="date" id="work_date" tabindex="1" class="time_date_picker text" size="8" value="<?php echo date('m/d/Y', $this->date); ?>" />
		<input type="submit" class="add" value="New" />
	</form>
	</p>
	
 	<table class="listing">
	<thead>
	<tr>
		<th class="left">Employee</th>
		<th class="left">Task</th>
		<th class="left">Hours</th>
		<th class="left">Per diem</th>
	</tr>
	</thead>
	<tbody>
	<?php 
	foreach ( $this->entries as $entry ) : $u = new User($entry['user_id']); $t = new Task($entry['task_id']); ?>
	<tr>
		<td><?php echo htmlentities($u->last_name . ', ' . $u->first_name); ?></td>
		<td><?php echo htmlentities($t->task_name); ?></td>
		<td><?php echo number_format($entry['hours'], 2); ?></td>
		<td><?php ($entry['per_diem']) ? print 'Yes' : print 'No';  ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
	
</div>

<?php $this->get('footer'); ?>
