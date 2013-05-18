<?php $this->get('header'); ?>

<?php echo $this->render('/admin/sidebar'); ?>

<div id="work_area">

	<h2>Manage Users</h2>
	<?php if ( count($this->users) > 0 ) : ?>
	<p>
		Manage your existing users or <a href="/admin/users/add">add a new user</a>. Showing page <?php echo $this->current_page; ?>
		of <?php echo $this->total_pages; ?>.
	</p>
	
	<?php echo $this->get('messaging'); ?>
	
	<?php echo $this->render('/admin/users/helpers/user_paging'); ?>
	
	<table class="listing">
	<thead>
	<tr>
		<th>User</th>
		<th>Email</th>
		<th>Type</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ( $this->users as $user ) : ?>
	<tr>
		<td>
			<a href="/admin/users/edit/<?php echo $user->ID; ?>" class="edit">
				<?php echo htmlentities($user->last_name . ', ' . $user->first_name); ?>
			</a>
			<div class="row-actions">
				<span>
					<a href="/admin/users/edit/<?php echo $user->ID; ?>" class="edit">Edit</a> | 
					<?php if ( $user->hidden ) : ?>
					<a href="/admin/users/unhide/<?php echo $user->ID; ?>" class="hide">Un-hide</a> | 
					<?php else : ?>
					<a href="/admin/users/hide/<?php echo $user->ID; ?>" class="">Hide</a> | 
					<?php endif; ?>
					<a href="/admin/users/delete/<?php echo $user->ID; ?>" class="delete">Delete</a>
				</span>
			</div>
		</td>
		<td>
			<a href="/admin/users/edit/<?php echo $user->ID; ?>" class="edit">
				<?php echo htmlentities($user->email); ?>
			</a>
		</td>
		<td><?php echo htmlentities($user->account_type); ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
	
	<?php echo $this->render('/admin/users/helpers/user_paging'); ?>
	
	<?php else: ?>
	
	<p>
		You have no users in your system, <a href="/admin/users/add">add a new user</a>.
	</p>
	
	<?php endif; ?>

</div>

<?php $this->get('footer'); ?>