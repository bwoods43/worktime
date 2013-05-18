<?php $this->get('header'); ?>

<?php echo $this->render('/managers/sidebar'); ?>

<div id="work_area" class="report">

	<h2><?php echo htmlentities($this->project->project_name); ?> : Project Summary</h2>

	<a href="/managers/reports">&laquo; Back to All Reports</a>

	<?php 
		$variance = $this->project->budget_hours - $this->project->getActualHours();
	?>
	
	<div class="chart">
		<h3>Hours Summary</h3>
		<?php
		// 7/3/10 baw - pie chart, so we need to determine percentages
		if ($this->project->getActualHours() >= $this->project->budget_hours) {
			$pct_hours_used = 100;
			$pct_hours_left = 0;	
		} else {		
			$pct_hours_used = ($this->project->getActualHours()/$this->project->budget_hours)*100;
			$pct_hours_left = 100 - $pct_hours_used;
		}		
		?>				
		<img src="http://chart.apis.google.com/chart?chs=450x250&cht=p3&chd=t:<?php echo $pct_hours_used; ?>,<?php 
			echo $pct_hours_left;
		?>&chdl=Hours+Used+<?php echo urlencode('('.$this->project->getActualHours().')'); ?>|Hours+Available+in+Budget+<?php 
			echo urlencode('(' . ($variance > 0 ? $variance : 0) . ')');
		?>&chco=B30037,d7d7d7&chp=3" alt="Hours Status" />
	</div>
	
	<div class="chart">
		<h3>Tasks Summary</h3>
		<!-- add this to the url string to go from 0 to 1000 - chds=0,1000 -->
		<img src="http://chart.apis.google.com/chart?chs=450x250&cht=bvs&chd=t:<?php 
		$tasks = $this->project->getTasks();
		$data = '';
		$ignore = array();
		foreach ( $tasks as $task_id ) {
			$time = $this->project->getTaskHours($task_id);
			if ( $time ) $data .= urlencode($time) . ',';
			else $ignore[] = $task_id;
		}
		$data = substr($data, 0, -1);
		echo $data;
		?>&chxt=x,y&chxl=0:|<?php
			$x_labels = '';
			foreach ( $tasks as $id ) {
				if ( !in_array($id, $ignore) ) {
					$task = new Task($id);
					//$x_labels .= urlencode($task->task_name) . '|';
					$x_labels .= substr($task->task_name, 0,6) . '|';
				}
			}
			$x_labels = substr($x_labels,0,-1);
			echo $x_labels;
		?>&chbh=r,1.3,1&chco=B30037" alt="Tasks Summary" />
	</div>


	<div class="chart">
		<h3>Tasks Summary Pie Chart</h3>
		<img src="http://chart.apis.google.com/chart?chs=475x250&cht=p3&chd=t:<?php 
		$tasks = $this->project->getTasks();
		$data = '';
		$ignore = array();
		foreach ( $tasks as $task_id ) {
			$time = $this->project->getTaskHours($task_id);
			if ( $time ) $data .= urlencode($time) . ',';
			else $ignore[] = $task_id;
		}
		$data = substr($data, 0, -1);
		echo $data;
		?>&chxt=x,y&chxl=0:|<?php
			$x_labels = '';
			foreach ( $tasks as $id ) {
				if ( !in_array($id, $ignore) ) {
					$task = new Task($id);
					//$x_labels .= urlencode($task->task_name) . '|';
					$x_labels .= substr($task->task_name, 0,6) . '|';
				}
			}
			$x_labels = substr($x_labels,0,-1);
			echo $x_labels;
		?>&chco=B30037,d7d7d7&chp=3" alt="Tasks Summary Pie Chart" />
	</div>
	
	<table class="report">
	<tbody>
	<tr>
		<th>Budgeted Hours</th>
		<td><?php echo number_format($this->project->budget_hours, 2); ?></td>
	</tr>
	<tr>
		<th>Actual Hours</th>
		<td><?php echo number_format($this->project->getActualHours(), 2); ?></td>
	</tr>
	<tr>
		<th>Hours Variance</th>
		<td><?php
			if ( $variance < 0 ) {
				echo '<span class="negative">';
			}
			else {
				echo '<span class="positive">';
			}
			echo number_format($variance, 2); 
		?></td>
	</tr>
	<tr>
		<th>Percent Variance</th>
		<td><?php
			$percent = $variance / $this->project->budget_hours;
			if ( $variance < 0 ) {
				echo '<span class="negative">';
			}
			else {
				echo '<span class="positive">';
			}
			echo number_format($percent * 100, 2) . '%';
		?></td>
	</tr>
	</table>

	<br />
	<form method="get" action="/managers/reports/overview" class="basic_form">
		<p>
			Jump to another project:
			<select name="project_id" id="project_id">
				<option value="">Choose Project...</option>
				<?php foreach ( $this->projects as $project ) : ?>
				<option value="<?php echo $project->ID; ?>"<?php if ( $project->ID = $this->project->ID ) echo ' selected="selected"'; ?>><?php 
					echo htmlentities($project->project_name);
				?></option>
				<?php endforeach; ?>
			</select>
			<input type="submit" class="button" value="View" />
		</p>
	</form>
	

	
</div>

<?php $this->get('footer'); ?>