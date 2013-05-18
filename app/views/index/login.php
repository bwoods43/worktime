<?php $this->get('header_login'); ?>

<div id="login">

	<h1>Please Login.</h1>
	<form method="post" action="/login">
		<?php $this->get('messaging'); ?>
		<p>
			<label for="username">
				Email:
			</label>
			<input type="text" id="username" name="email" value="<?php echo htmlentities($this->clean['email']); ?>" tabindex="1" class="text" />
		</p>
		<p>
			<label for="passwd">
				Password:
			</label>
			<input type="password" id="passwd" name="passwd" value="" tabindex="1" class="text" />
		</p>
		<p>
			<input type="image" src="/images/btn_sign_in.png" class="button_image" value="Sign In" tabindex="1" />
			<a href="/forgot" class="forgot">Forgot?</a>
		</p>
	
	</form>
 
</div>

<?php $this->get('footer_login'); ?>