<p>Dear <?php echo htmlentities($this->user->first_name); ?>,</p>

<p>An account has been created for you at http://<?php echo $_SERVER['HTTP_HOST']; ?>. 
Please login by visiting http://<?php echo $_SERVER['HTTP_HOST']; ?>/login and using the 
email and password below:</p>

<p>
	<strong style="width:130px;">Email:</strong> <?php echo htmlentities($this->user->email); ?>
</p>
<p>
	<strong style="width:130px;">Password:</strong> <?php echo htmlentities($this->password); ?>
</p>