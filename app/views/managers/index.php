<?php $this->get('header'); ?>

<?php echo $this->render('/managers/sidebar'); // 7/3/10 baw - show sidebar ?>

<div id="time_entry">

	<h2>Welcome, <?php echo htmlentities($this->user->first_name . ' ' . $this->user->last_name); ?></h2>
	
	<?php if ( count($this->projects) > 0 ) : ?>

	<form method="post" action="" class="basic_form start_form">
		
		<p>
			<label for="entry[project_id]">
				Choose Project:
			</label>
			<select name="project_id" id="project_id" tabindex="1">
				<option value=""></option>
				<?php if ( count($this->projects) > 0 ) : foreach ( $this->projects as $project ) : ?>
				<option value="<?php echo $project->ID; ?>"<?php
					if ( $this->clean['project_id'] == $project->ID ) echo ' selected="selected"';
				?>><?php echo htmlentities($project->project_name); ?></option>
				<?php endforeach; endif; ?>
			</select>
		</p>
		<p class="requires_project">
			<label for="work_date">
				Choose Date:
			</label>
			<input type="text" name="work_date" id="work_date" tabindex="1" class="time_date_picker text" size="8" value="<?php echo date("m/d/Y"); ?>" />
		</p>
		<p class="requires_project">
			<input type="submit" value="Enter Time" class="button" name="action" />
			<a href="#" class="view_time">View Timesheets</a>
		</p>

	</form>

	<?php else : ?>
	
	<p><em>You currently have no assigned projects available for reporting.</em></p>
	
	<?php endif; ?>

</div>

<?php $this->get('footer'); ?>
