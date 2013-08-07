<?php $this->get('header'); ?>

<div id="time_review">

	<?php echo $this->render('/managers/jumper'); ?>

	<h2>
		<small>Review Time Entries</small> <span class="small-header">(<a href="/managers/add/<?php echo htmlentities($this->project->ID); ?>/?date=<?php echo htmlentities(date("m/d/Y", $this->date)); ?>">Add new entries for this project and date</a>)</span>
		<br />
		<?php echo htmlentities($this->project->project_name); ?> : <?php echo date("F j, Y", $this->date); ?>
	</h2>
	
	<input id="project_id" type="hidden" value="<?php echo $this->project->ID; ?>" />
	<input id="work_date" type="hidden" value="<?php echo date("m/d/Y", $this->date); ?>" />
	
	<div id="the_note">
		<strong>Daily Note(s):</strong>
		<div class="note_text">
			<span class="data">
				<?php
					$the_project = new Project($this->project->ID);
					$existing_note = $the_project->getNote($this->date);
				?>
				<?php echo  !empty($existing_note) ? nl2br(htmlentities($existing_note)) : 'Empty'; ?>
			</span>
			<input type="hidden" id="note_value" value="<?php echo htmlentities($existing_note); ?>" />
			<div class="right buttons">
				<input type="button" class="edit" value="Edit Note" />
			</div>
		</div>
	</div>
	
	<?php if ( count($this->entries) > 0 ) : ?>	
	
	<table class="listing">
	<thead>
	<tr>
		<th>Employee</th>
		<th>Task</th>
		<th>Hours</th>
		<th>Per diem</th>
		<th>Options</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ( $this->entries as $k => $entry ) : ?>
	<tr id="entry_<?php echo $k+1; ?>">
		<td class="employee">
			<span class="data"><?php echo htmlentities($entry->last_name .', ' . $entry->first_name); ?></span>
			<input type="hidden" class="employee" id="employee_<?php echo $k+1; ?>" value="<?php echo $entry->user_id; ?>" />
		</td>
		<td class="task">
			<span class="data"><?php echo htmlentities($entry->task_name); ?></span>
			<input type="hidden" class="task" id="task_<?php echo $k+1; ?>" value="<?php echo $entry->task_id; ?>" />
		</td>
		<td class="hours">
			<span class="data"><?php echo number_format($entry->hours, 2); ?></span>
			<input type="hidden" class="hours" id="hours_<?php echo $k+1; ?>" value="<?php echo $entry->hours; ?>" />
		</td>
		<td class="per_diem">
			<span class="data"><?php ($entry->per_diem) ? print 'Yes' : print 'No';  ?></span>
			<input type="hidden" class="per_diem" id="per_diem_<?php echo $k+1; ?>" value="1" />
		</td>
		<td class="options">
			<input type="button" class="button edit" value="Edit" />
			<input type="button" class="button delete" value="Delete" />
		</td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
	
	<?php else: ?>
	
	<p><em>There are no time entries for this day. <a href="/managers/add/<?php echo $this->project->ID; ?>/?date=<?php echo htmlentities($_GET['date']); ?>">Enter Time &raquo;</a></em></p>

	<?php endif; ?>


</div>

<?php $this->get('footer'); ?>
