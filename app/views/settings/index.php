<?php $this->get('header'); ?>
<?php $this->get('messaging'); ?>

<h2>Account Settings</h2>

<form method="post" action="" class="basic_form">
	<div style="float:left;margin-right: 4em;">
		<h3 style="margin-top:0;">Personal Information</h3>
		<p>
			<label for="user[first_name]">First Name:</label>
			<input type="text" id="user[first_name]" name="user[first_name]" tabindex="1" class="text" value="<?php
				echo htmlentities($this->clean['user']['first_name']);
			?>" />
		</p>
		<p>
			<label for="user[last_name]">Last Name:</label>
			<input type="text" id="user[last_name]" name="user[last_name]" tabindex="1" class="text" value="<?php
				echo htmlentities($this->clean['user']['last_name']);
			?>" />
		</p>
		<p>
			<label for="user[email]">Email Address:</label>
			<input type="text" id="user[email]" name="user[email]" tabindex="1" size="40" class="text" value="<?php
				echo htmlentities($this->clean['user']['email']);
			?>" />
		</p>
	</div>
	
	<h3 style="margin-top:0;">Change Password</h3>
	<p>
		<label for="passwd[current]">Current Password:</label>
		<input type="text" id="passwd[current]" name="passwd[current]" tabindex="1" class="text" value="" />
	</p>
	<p>
		<label for="passwd[new]">New Password:</label>
		<input type="text" id="passwd[new]" name="passwd[new]" tabindex="1" class="text" value="" />
	</p>
	<p class="clear">
		<input type="submit" name="basic_info" value="Save Changes" class="button" tabindex="1" />
	</p>
</form>

<?php $this->get('footer'); ?>