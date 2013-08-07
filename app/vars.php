<?php
/*
 * Define variables to be used to differentiate between offices
 * 
 */
 //echo "<PRE>";
 //print_r($_SESSION);
 
$vars = array();
 
$vars['per_diem_changes_2010_date'] = '2010-12-10';
 
switch ($_SESSION['beynon_office_id']) {
	case 2: // oregon
		$vars['rain_task_id'] = 205;
		$vars['drive_task_id'] = 2010;
		$vars['use_state_for_project'] = TRUE;
	break;
	default: // 1 is maryland 
		$vars['rain_task_id'] = 5;
		$vars['drive_task_id'] = 10;
		$vars['use_state_for_project'] = FALSE;
	break;
}
