	<div id="jumper">
		<select>
			<option value="">Change Projects...</option>
			<?php

/*
// 7/2/10 baw - instead of showing only projects assigned to manager, show all projects
				$params = array("manager_id" => User::getCurrentUser()->ID);
				if ( User::isAdmin() ) $params = array();
				$projects = Project::getProjects($params);
*/
				$projects = Project::getProjects(array());
				foreach ( $projects as $p ) : ?>
			<option value="/managers/<?php echo UB_Request::getInstance()->getAction(); ?>/<?php echo $p->ID; ?>/"><?php echo htmlentities($p->project_name); ?></option>
			<?php endforeach; ?>
		</select>
		or change date:
		<input type="hidden" class="date_picker" title="Change Date" />
	</div>