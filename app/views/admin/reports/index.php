<?php $this->get('header'); ?>

<?php echo $this->render('/admin/sidebar'); ?>

<div id="work_area" class="report">

	<h2>Reports</h2>
	
	<form method="get" action="/admin/reports/overview" class="basic_form">
		<h3>Project Overview</h3>
		<p>
			<select name="project_id" id="project_id">
				<option value="">Choose Project...</option>
				<?php foreach ( $this->projects as $project ) : ?>
				<option value="<?php echo $project->ID; ?>"<?php if ( $project->ID = $this->project->ID ) echo ' selected="selected"'; ?>><?php 
					echo htmlentities($project->project_name);
				?></option>
				<?php endforeach; ?>
			</select>
			<input type="submit" class="button" value="View Project Overview" />
		</p>
	</form>
	
	<P><a href="/admin/reports/timesheet">Per diem report - by employee</a> &nbsp;|&nbsp; <a href="/admin/reports/timesheet?report_type=by-project">Per diem report - by project</a></p>
	<!--
	<hr />
	
	<form method="get" action="/admin/reports/timesheet" class="basic_form">
		<h3>Employee Weekly Timesheet</h3>
		<p>
			Generate a time sheet for: <br />
			<select name="employee_id" id="employee_id">
				<option value="">Choose Employee...</option>
				<?php foreach ( $this->employees as $employee ) : ?>
				<option value="<?php echo $employee->ID; ?>"<?php if ( $employee->ID = $this->employee->ID ) echo ' selected="selected"'; ?>><?php 
					echo htmlentities($employee->last_name . ', ' . $employee->first_name);
				?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			For the week of:
			<br />
			<input type="text" id="the_date" name="dt" value="" size="10" class="text date_picker" />
		</p>
		<p>			
			View as:<br />
			<select id="type" name="type">
				<option value="html">web page</option>
				<option value="csv">CSV/Excel Download</option>
			</select>
			
		</p>
		<p>			
			<input type="submit" class="button" value="View Employee Timesheet" />
		</p>
	</form>
	
	-->
	
	
</div>

<?php $this->get('footer'); ?>