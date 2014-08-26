<?php if (!empty($campaigns)) : ?>
	<table class="widefat">
	
		<tbody>
			<?php foreach ($campaigns as $campaign) : ?>
				<tr>
					<th></th>
					<td><?php echo $campaign -> title; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else : ?>
	<p class="error"><?php _e('No campaigns were found, please add one.', $this -> plugin_name); ?></p>
<?php endif; ?>