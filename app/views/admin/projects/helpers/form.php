		<p>
			<label for="project[project_name]">
				Project Name:
			</label>
			<input type="text" name="project[project_name]" id="project[project_name]" tabindex="1" class="text" value="<?php echo htmlentities($this->clean['project']['project_name']); ?>" size="60" />
		</p>
		<p>
			<label for="project[manager_id]">
				Manager:
			</label>
			<select name="project[manager_id]" id="project[manager_id]" tabindex="1">
				<option value="">- Unassigned -</option>
				<?php if ( count($this->managers) > 0 ) : foreach ( $this->managers as $user ) : ?>
				<option value="<?php echo $user->ID; ?>"<?php if ( $user->ID == $this->clean['project']['manager_id'] ) echo ' selected="selected"'; ?>><?php
					echo htmlentities( $user->last_name .', ' . $user->first_name);
				?></option>
				<?php endforeach; endif; ?>
			</select>
		</p>
		<p>
			<label for="project[start_date]">
				Start Date:
			</label>
			<input type="text" name="project[start_date]" id="project[start_date]" tabindex="1" class="text date_picker" value="<?php echo $this->clean['project']['start_date'] ? htmlentities(date("m/d/Y", strtotime($this->clean['project']['start_date']))) : ''; ?>" />
		</p>
		<p>
			<label for="project[budget_end_date]">
				Estimated End Date:
			</label>
			<input type="text" name="project[budget_end_date]" id="project[budget_end_date]" tabindex="1" class="text date_picker" value="<?php echo $this->clean['project']['budget_end_date'] ? htmlentities(date("m/d/Y", strtotime($this->clean['project']['budget_end_date']))) : ''; ?>" />
		</p>
		<p>
			<label for="project[budget_hours]">
				Budgeted Hours:
			</label>
			<input type="text" name="project[budget_hours]" id="project[budget_hours]" tabindex="1" class="text" value="<?php echo htmlentities($this->clean['project']['budget_hours']); ?>" size="4" />
		</p>
		<?php if ( count($this->tasks) > 0 ) : ?>
		<p>
			<label for="project[tasks]">
				Assign Tasks:
			</label>
			<select name="project[tasks][]" id="project[tasks]" multiple="multiple" size="8" class="multiple_select">
				<?php foreach ( $this->tasks as $task ) : ?>
				<option value="<?php echo $task->ID; ?>"<?php if ( is_array($this->clean['project']['tasks']) && in_array($task->ID, $this->clean['project']['tasks']) ) echo ' selected="selected"'; ?>>
				<?php echo htmlentities($task->task_name); ?>
				</option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php endif; ?>
		<?php if ( count($this->employees) > 0 ) : ?>
		<p>
			<label for="project[employees]">
				Assign Employees:
			</label>
			<select name="project[employees][]" id="project[employees]" multiple="multiple" size="8" class="multiple_select">
				<?php foreach ( $this->employees as $employee ) : ?>
				<option value="<?php echo $employee->ID; ?>"<?php if ( is_array($this->clean['project']['employees']) && in_array($employee->ID, $this->clean['project']['employees']) ) echo ' selected="selected"'; ?>>
				<?php echo htmlentities($employee->last_name . ', ' . $employee->first_name); ?>
				</option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php endif; ?>
