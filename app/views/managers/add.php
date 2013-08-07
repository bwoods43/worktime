<?php $this->get('header'); ?>

<div id="time_entry">

	<?php echo $this->render('/managers/jumper'); ?>

	<h2>
		<small>Time Entry</small> <span class="small-header">(<a href="/managers/review/<?php echo htmlentities($this->project->ID); ?>/?date=<?php echo htmlentities(date("m/d/Y", $this->date)); ?>">Edit existing entries for this project and date</a>)</span>
		<br />
		<?php echo htmlentities($this->project->project_name); ?> : 
		<span class="work_date"><?php echo date("F j, Y", $this->date); ?></span>
<!--		<input type="hidden" class="time_date_picker" value="" />-->
	</h2>
	
	<form method="post" action="" class="basic_form">
		
		<?php if ( $this->error_msg ) : ?>		
		<div class="errors"><p><?php echo $this->error_msg; ?></p></div>
		<?php endif; ?>
		
		<input type="hidden" id="project_id" name="project_id" value="<?php echo htmlentities($this->project->ID); ?>" />
		<input type="hidden" id="work_date" name="date" value="<?php echo htmlentities(date("m/d/Y", $this->date)); ?>" />
		
		
		<div class="the_note">
			<label for="the_note">
				Daily Note(s):
			</label>
			<textarea id="the_note" name="note" rows="4" cols="80" tabindex="1" ><?php echo htmlentities($this->clean['note']); ?></textarea>
		</div>
		
		<div id="lines">
		
		<?php if ( count($this->clean['employees']) <= 0 ) : ?>
			<div class="line">
				<div class="employee">
					<label>Employee</label>
					<select name="employees[]" tabindex="1">
						<option value="">Choose Employee...</option>
						<?php foreach ( $this->employees as $user_id ) : $user = new User($user_id); ?>
						<option value="<?php echo $user->ID; ?>"><?php echo htmlentities($user->last_name . ', ' . $user->first_name); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="task">
					<label>Task</label>
					<select name="tasks[]" tabindex="1">
						<option value="">Choose Task...</option>
						<?php foreach ( $this->tasks as $task_id ) : $task = new Task($task_id); ?>
						<option value="<?php echo $task->ID; ?>"><?php echo htmlentities($task->task_name); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="hours">
					<label>Hours</label>
					<input type="text" class="text" name="hours[]" value="" size="3" tabindex="1" />
				</div>
				<div class="per_diem">
					<label>Per diem?</label>
					<input type="checkbox" class="checkbox" name="per_diem[]" value="1" checked="checked"  tabindex="1" />
				</div>				
				<div class="add">
				</div>
				<div class="clear"></div>
			</div>		
		<?php else: foreach ( $this->clean['employees'] as $k => $employee ) : ?>
			<div class="line<?php if ( $this->errors[$k] ) echo ' highlight'; ?>">
				<?php if ( $this->errors[$k] && $this->errors[$k] !== true ) : ?>
				<div class="inline_error"><?php echo $this->errors[$k]; ?></div>
				<?php endif; ?>
				<div class="employee">
					<label>Employee</label>
					<select name="employees[]" tabindex="1">
						<option value="">Choose Employee...</option>
						<?php foreach ( $this->employees as $user_id ) : $user = new User($user_id); ?>
						<option value="<?php echo $user->ID; ?>"<?php 
							if ( $employee == $user->ID ) echo ' selected="selected"';
						?>><?php echo htmlentities($user->last_name . ', ' . $user->first_name); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="task">
					<label>Task</label>
					<select name="tasks[]" tabindex="1">
						<option value="">Choose Task...</option>
						<?php foreach ( $this->tasks as $task_id ) : $task = new Task($task_id); ?>
						<option value="<?php echo $task->ID; ?>"<?php 
							if ( $this->clean['tasks'][$k] == $task->ID ) echo ' selected="selected"';
						?>><?php echo htmlentities($task->task_name); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="hours">
					<label>Hours</label>
					<input type="text" class="text" name="hours[]" value="<?php echo htmlentities($this->clean['hours'][$k]); ?>" size="3" tabindex="1" />
				</div>
				<div class="per_diem">
					<label>Per diem</label>
					<input type="text" class="text" name="per_diem[]" value="1" <?php 
							if ( $this->clean['per_diem'][$k] == 1 ) echo ' checked="checked"';
						?>> tabindex="1" />
				</div>				
				<div class="add">
					<?php if ( $k ) : ?>
					<span class="remove_button">X Remove</span>
					<?php endif; ?>
				</div>
				<div class="clear"></div>
			</div>
		<?php endforeach; endif; ?>
		</div>		
		
		<div class="clear submit">
			<span class="add_button">+ Add Line</span>
			<p>
				<input type="submit" value="Save" class="button submit" />
			</p>
		</div>

	</form>

</div>

<?php $this->get('footer'); ?>
