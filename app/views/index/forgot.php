<?php $this->get('header_login'); ?>

	<h1>Reset Your Password.</h1>
	<form method="post" action="">
	
	<?php $this->get('messaging'); ?>
		
	<?php if ( !$this->success ) : ?>
		<p>Enter your email address below to reset your password:</p>
		
		<p><input type="text" id="email" name="email" value="<?php echo htmlentities($this->clean['']); ?>" tabindex="1" size="50" class="text" /></p>
		<p><input type="submit" class="button" value="Reset my password" tabindex="1" /></p>
	<?php endif; ?>
	
	</form>

<?php $this->get('footer_login'); ?>