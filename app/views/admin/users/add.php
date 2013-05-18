<?php $this->get('header'); ?>

<?php echo $this->render('/admin/sidebar'); ?>

<div id="work_area">

	<h2>Add a User</h2>
	<form method="post" action="" class="basic_form">
	
		<?php $this->get('messaging'); ?>

		<?php echo $this->render('/admin/users/helpers/form'); ?>	

		<p>
			<label for="passwd[passwd]">
				Password:
			</label>
			<input type="password" id="passwd[passwd]" name="passwd[passwd]" tabindex="1" value="" class="text" />
		</p>
		<p>
			<label for="passwd[confirm]">
				Confirm Password:
			</label>
			<input type="password" id="passwd[confirm]" name="passwd[confirm]" tabindex="1" value="" class="text" />
		</p>

		<p>
			<label for="notify" class="checkbox">
				<input type="checkbox" name="notify" id="notify" value="1" <?php if ( $this->clean['notify'] ) echo 'checked="checked" '; ?>tabindex="1" />
				Notify user of login/password via email.
			</label>
		</p>

		<p>
			<input type="submit" name="basic_info" value="Add User" class="button" tabindex="1" />
		</p>
	
	</form>
	
</div>

<?php $this->get('footer'); ?>