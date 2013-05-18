<?php $this->get('header'); ?>

<?php echo $this->render('/admin/sidebar'); ?>

<div id="work_area">

	<h2>Projects</h2>
	<?php if ( count($this->projects) > 0 ) : ?>
	<p>
		Manage your <a href="/admin/projects">existing projects</a>, <a href="/admin/projects/add">add a new project</a> or manage <a href="/admin/projects?archived=1">archived projects</a>.
	</p>
	
	<table class="listing">
	<thead>
	<tr>
		<th>Project</th>
		<th>Manager</th>
		<th style="text-align:center">Start Date</th>
		<th style="text-align:center">Estimated Finish</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ( $this->projects as $project ) : ?>
	<tr>
		<td>
			<a href="/admin/projects/edit/<?php echo $project->ID; ?>" class="edit">
				<?php echo htmlentities($project->project_name); ?>
			</a>
			<div class="row-actions">
				<span>
					<a href="/admin/projects/edit/<?php echo $project->ID; ?>" class="edit">Edit</a> | 

<?php
//8/1/11 baw archive/unarchive link depending on the need
if ($_GET['archived'] == 1) {
	print '<a href="/admin/projects/unarchive/' . $project->ID . '" class="unarchive">Unarchive</a> | ';
} else {	
	print '<a href="/admin/projects/archive/' . $project->ID . '" class="archive">Archive</a> | ';
}
?>	
					<a href="/admin/projects/delete/<?php echo $project->ID; ?>" class="delete">Delete</a>
				</span>
			</div>
		</td>
		<td>
			<?php 
				$manager = new User($project->manager_id);
				if ( $manager->first_name != '' ) : 
			?>
			<a href="/admin/users/edit/<?php echo $manager->ID; ?>">
				<?php echo htmlentities($manager->first_name . ' ' . $manager->last_name); ?> 
			</a>
			<?php else: ?>
			<em>Unassigned</em>
			<?php endif; ?>
		</td>
		<td style="text-align:center">
			<?php echo date("m/d/y", strtotime($project->start_date)); ?>
		</td>
		<td style="text-align:center">
			<?php echo date("m/d/y", strtotime($project->budget_end_date)); ?>
		</td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
	
	<?php else: ?>
	
	<p>
		You have no <?php ($_GET['archived'] == 1 ? print 'archived ' : '');?>projects in your system, <a href="/admin/projects/add">add a new one</a>.
	</p>
	
	<?php endif; ?>

</div>

<?php $this->get('footer'); ?>