<div class="wrap">
	<h2><?php _e('Order Custom Fields', $this -> name); ?></h2>
	
	<div class="subsubsub" style="float:none;"><?php echo $Html -> link(__('&larr; Manage All Fields', $this -> plugin_name), $this -> url); ?></div>

	<?php if (!empty($fields)) : ?>
		<ul style="margin:0; padding:0;" id="<?php echo $this -> pre; ?>fields">
			<?php foreach ($fields as $field) : ?>
				<li id="fields_<?php echo $field -> id; ?>" style="width:100%; display:block;" class="<?php echo $this -> pre; ?>lineitem widefat"><?php _e($field -> title); ?></li>
			<?php endforeach; ?>
		</ul>
		
		<script type="text/javascript">
		jQuery(document).ready(function() {		
			jQuery("ul#<?php echo $this -> pre; ?>fields").sortable({
				start: function(request) {
					jQuery("#<?php echo $this -> pre; ?>message").slideUp();
				},
				stop: function(request) {					
					jQuery("#<?php echo $this -> pre; ?>message").load(wpmlAjax + "?cmd=fields_order", jQuery("ul#<?php echo $this -> pre; ?>fields").sortable('serialize')).slideDown("slow");
				},
				axis: "y"
			});
		});
		</script>
		
		<p id="<?php echo $this -> pre; ?>message" class="<?php echo $this -> pre; ?>error"><?php _e('Drag and drop the custom fields below to order/sort them.', $this -> name); ?></p>
	<?php else : ?>
		<p class="<?php echo $this -> pre; ?>error"><?php _e('No custom fields were found', $this -> name); ?></p>													
	<?php endif; ?>
</div>