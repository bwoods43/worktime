	<?php if ( $this->total_pages > 1 ) : ?>
	<p class="right paging">
		<?php if ( $this->total_pages > 1 && $this->current_page > 1 ) : ?>
		<a href="?start=<?php echo $this->current_page - 2; ?>">&laquo;</a>
		<?php endif; ?>
		
		<?php for ( $i = 1; $i <= $this->total_pages; $i++ ) : ?>
			<?php if ( $i == $this->current_page ) : echo "<span>$i</span>"; else: ?>
			<a href="?start=<?php echo $i-1; ?>"><?php echo $i; ?></a>
			<?php endif;  ?>
		<?php endfor; ?>
		
		<?php if ( $this->total_pages > 1 && $this->current_page < $this->total_pages ) : ?>
		<a href="?start=<?php echo $this->current_page; ?>">&raquo;</a>
		<?php endif; ?>
	</p>
	<?php endif; ?>