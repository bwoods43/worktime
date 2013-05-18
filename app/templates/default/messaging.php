
<?php if ( $this->success ) : ?>
<div class="success">
	<?php echo $this->success; ?>
</div>
<?php endif; ?>

<?php if ( count($this->errors) > 0 ) : ?>
<div class="errors">
	<ul>
		<li><?php echo implode('</li><li>', $this->errors); ?></li>
	</ul>
</div>
<?php endif; ?>