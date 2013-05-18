<?php $this->get('header'); ?>

<?php echo $this->render('/admin/sidebar'); ?>

<div id="work_area">

<?php if ( $this->success) : ?>

	<h2>Project Archived</h2>
	
	<?php $this->get('messaging'); ?>

<?php else : ?>

	<h2>Archive <q><?php echo htmlentities($this->project->project_name); ?></q> Project</h2>

	<form method="post" action="" class="basic_form">
		
		<h3>Are You Sure You Want To Archive This Project?</h3>
		
		<div class="errors">
			<strong>You are about to archive this project, including all timesheet entries for the project
			and project reports.</strong>
		</div>
		
		<p>Press the button below to archive the project and all related project data.</p>
		
		<p>
			<input type="hidden" name="archive[ID]" value="<?php echo $this->project->ID; ?>" />
			<input type="submit" value="Archive Project" class="button" />
		</p>
	
	</form>

<?php endif; ?>

</div>

<?php $this->get('footer'); ?>