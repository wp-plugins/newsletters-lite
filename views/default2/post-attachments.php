<?php if (!empty($attachments)) : ?>
	<h3><?php _e('Attached Files', $this -> plugin_name); ?></h3>
	<p class="newsletters_attachments">
		<ul>
			<?php foreach ($attachments as $attachment) : ?>
				<li><?php echo $Html -> attachment_link($attachment, false, 999); ?></li>
			<?php endforeach; ?>
		</ul>
	</p>
<?php endif; ?>