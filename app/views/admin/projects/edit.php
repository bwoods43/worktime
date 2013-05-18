<?php $this->get('header'); ?>

<?php echo $this->render('/admin/sidebar'); ?>

<div id="work_area">

	<h2>Edit Project</h2>

	<?php $this->get('messaging'); ?>

	<form method="post" action="" class="basic_form">
		
		<?php echo $this->render('/admin/projects/helpers/form'); ?>		
		<p>
			<input type="submit" value="Save Changes" class="button" />
		</p>
	
	</form>

</div>

<?php $this->get('footer'); ?>