<?php if (empty($_GET["type"])) $this->get('header'); ?>

<?php
// set start and end dates ... needed throughout
$start_date = $this->start;
$end_date = $this->end;

function createFriendlyDate($date, $format = 'F j') {
	list($yr,$mon,$day) = split('-',$date);
	return date($format, mktime(0,0,0,$mon,$day,$yr)); 
}

// 8/4/10 baw creating this as a function to show headers on each printed page
function addHeaders($start_date, $end_date) {

		echo '<tr class="main-header-row"><td class="end-cell">&nbsp;</td>';
		echo '<td class="weekdays header end-cell" colspan="3">Monday</td>';
		echo '<td class="weekdays header end-cell" colspan="3">Tuesday</td>';
		echo '<td class="weekdays header end-cell" colspan="3">Wednesday</td>';
		echo '<td class="weekdays header end-cell" colspan="3">Thursday</td>';
		echo '<td class="weekdays header end-cell" colspan="3">Friday</td>';
		echo '<td class="weekdays header end-cell"colspan="3">Saturday</td>';
		echo '<td class="weekdays header end-cell" colspan="3">Sunday</td>';
		echo '<td class="weekdays header end-cell" colspan="2">&nbsp;</td>';
		echo '<td class="header">Per diem</td>';
		echo '</tr>';
		
		// create placeholders for each day		
		echo '<tr><td class="end-cell">&nbsp;</td>';
		

		$check_date = $start_date;
		while ($check_date <= $end_date) {
			echo '<td class="weekdays header end-cell" colspan="3">' . createFriendlyDate($check_date) . '</td>';
    	$check_date = date ("Y-m-d", strtotime ("+1 day", strtotime($check_date)));
		}
		
		echo '<td class="header end-cell">TOTAL</td><td class="header end-cell">TOTAL</td><td class="header end-cell"># of days</td></tr>';
		
		// create headers for task types
		echo '<tr><td class="end-cell">&nbsp;</td>';
		$check_date = $start_date;
		while ($check_date <= $end_date) {
			echo '<td class="header">Site</td><td class="header">Drive</td><td class="header end-cell">Rain</td>';
    	$check_date = date ("Y-m-d", strtotime ("+1 day", strtotime($check_date)));
		}
		
		echo '<td class="header end-cell">SITE</td><td class="header end-cell">Drive/Rain</td><td>&nbsp;</td></tr>';

}
?>

<div>

	<h2>Timesheet</h2>


<?php if (empty($_GET["type"]))	{ ?>

	<a href="/admin/reports">&laquo; Back to All Reports</a>
	
	<div class="chart">

	<form method="post" action="" class="basic_form start_form">

		<p class="requires_project">
			<label for="work_date">
				Choose Date:
			</label>
			<input type="text" name="work_date" id="work_date" tabindex="1" class="time_date_picker text" size="8" value="<?php ($end_date ? print createFriendlyDate($end_date, 'm/d/Y') : print date('m/d/Y')); ?>" />

			&nbsp;&nbsp;&nbsp;<input type="submit" value="View per diem report" class="button" name="action" />
		</p>
	</form>
<? } ?>
		
		<?php 
		echo '<h3>Report for ' . createFriendlyDate($start_date, 'F j, Y') . " to " . createFriendlyDate($end_date, 'F j, Y') . "</h3>";

	if (empty($_GET["type"]))	{
		echo '<p><a href="/admin/reports/timesheet?type=download&work_date=' . createFriendlyDate($end_date, 'm/d/Y') . '&report_type=' . $_GET['report_type']. '" target="_blank">Download report</a></p>';
		echo '<p><a href="JavaScript:window.print();">Print this page</a></p>';
	}
		
		echo '<table class="timesheet">';

		if ($this->errors) {
			echo '<tr>';
			foreach ($this->errors as $error) {
				echo '<td class="name-row header">' . $error . '</td>';
			}
			echo '</tr>';
		
		} else {

			if ($_GET['report_type'] != 'by-project') {
				addHeaders($start_date, $end_date);
			}
			
// 8/4/10 baw add row counter to add headers on multi pages
			$row_counter = 0;
			
			foreach ($this->entries as $entry) {
						
				if ($_GET['report_type'] != 'by-project') {
					if ($row_counter >= 20)  {
						addHeaders($start_date, $end_date);
						$row_counter = 0;
					}
				}

				if ($_GET['report_type'] == 'by-project') {
						addHeaders($start_date, $end_date);
				}

				// 1/10/11 sometimes, there's no per diem ... account for this
				if ($entry['per diem']) {
					$per_diem_total = array_sum($entry['per diem']);
				} else {
					$per_diem_total = 0;
				}

				echo '<tr><td class="name-row" colspan="25">' . $entry['parent']  . ' (per diem: ' . $per_diem_total .')</td></tr>';
				$row_counter++;
								
				foreach ($entry['children'] as $child) {

					// zero out totals here
					$regular = $drive_rain = $days_at_work = 0;

					echo '<tr><td class="secondary-name-row" colspan="25">' . $child['child'] . '</td></tr>';
					
					echo '<tr><td class="end-cell">&nbsp;</td>';
					
					$color_counter = 0;
					foreach ($child['days'] as $day) {
						if ($color_counter&1) {
							$alt_color_class = '';
						} else {
							$alt_color_class = 'shaded-cell';
						}
						echo '<td class="' . $alt_color_class . '">' . $day['regular'] . '</td>';
						echo '<td class="' . $alt_color_class . '">' . $day['drive'] . '</td>';
						echo '<td class="end-cell ' . $alt_color_class . ' ">' . $day['rain'] . '</td>';
						
						// add up the totals here
						$regular += $day['regular'];
						$drive_rain += $day['drive'] + $day['rain'];
						
						// day at work if a work record exists
						//if ($day['regular'] > 0 || $day['drive'] > 0 || $day['rain'] > 0) {
						if ($day['per diem'] == 1) {
							$days_at_work++;
						}
						$color_counter++;
					}
					
					echo '<td>' . $regular . '</td>';
					echo '<td>' . $drive_rain . '</td>';
// 8/4/10 baw do not show per diem if project is yard	
					echo '<td>' . ($child['child'] == 'Yard' ? 0:$days_at_work) . '</td>';

					echo '</tr>';
					$row_counter++;
				}

		}
		
		
		}
		
		echo "</table>";
		?>		
	</div>
<?php if (empty($_GET["type"]))	{ ?>
	<br />
	<form method="get" action="/admin/reports/overview" class="basic_form">
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
<?php } ?>	
</div>

<?php if (empty($_GET["type"])) $this->get('footer'); ?>
<?php 
	if ($_GET["type"] == "download") {
	require_once "/home/jfisher/public_html/beynontime.com/app/lib/xlsstream/excel.php";
	$export_file = "xlsfile://home/jfisher/public_html/beynontime.com/tmp/example.xls";
	$fp = fopen($export_file, "wb");
	if (!is_resource($fp))
	{
    die("Cannot open $export_file");
	}
	
	//fwrite($fp, serialize($this->entries));
	fclose($fp);
	
	// open excel file
	header ("Content-Type: application/x-msexcel");
  header ("Content-Disposition: attachment; filename=\"example.xls\"" );
  readfile("xlsfile://home/jfisher/public_html/beynontime.com/tmp/example.xls");
  exit;
}