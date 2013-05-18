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
		<p>
			<label for="user[account_type]">Account Type:</label>
			<select id="user[account_type]" name="user[account_type]" tabindex="1">
			<?php foreach ( User::getAccountTypes() as $label => $type ) : ?>
				<option value="<?php echo htmlentities($type); ?>"<?php if ( $this->clean['user']['account_type'] == $type ) echo ' selected="selected"'; ?>><?php 
					echo htmlentities($label);
				?></option>
			<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="user[hidden]">
				<input type="checkbox" id="user[hidden]" name="user[hidden]" value="1" <?php 
					if ( $this->clean['user']['hidden'] == 1 ) echo 'checked="checked"';
				?> /> Hide this user from employee lists 
			</label>
		</p>