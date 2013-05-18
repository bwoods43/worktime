<?php $this->get('header'); ?>

<?php echo $this->render('/admin/sidebar'); ?>

<div id="work_area">

	<h2>Edit User : <?php echo htmlentities($this->user->first_name . ' ' . $this->user->last_name)?></h2>
	<form method="post" action="" class="basic_form">
	
		<?php $this->get('messaging'); ?>

		<?php echo $this->render('/admin/users/helpers/form'); ?>	
		
		<p class="note">
			<strong>Note:</strong> Leave password fields blank to keep existing password.
		</p>
		
		<p>
			<label for="passwd[passwd]">
				New Password:
			</label>
			<input type="password" id="passwd[passwd]" name="passwd[passwd]" tabindex="1" value="" class="text" />
		</p>
		<p>
			<label for="passwd[confirm]">
				Confirm New Password:
			</label>
			<input type="password" id="passwd[confirm]" name="passwd[confirm]" tabindex="1" value="" class="text" />
		</p>
		
		<p>
			<input type="hidden" name="user[ID]" value="<?php echo $this->clean['user']['ID']; ?>" />
			<input type="submit" name="basic_info" value="Save Changes" class="button" tabindex="1" />
		</p>
	
	</form>
	
</div>

<?php $this->get('footer'); ?>