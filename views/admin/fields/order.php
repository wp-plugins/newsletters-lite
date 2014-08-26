<div class="wrap newsletters">
	<h2><?php _e('Order Custom Fields', $this -> name); ?></h2>
	
	<div class="subsubsub" style="float:none;"><?php echo $Html -> link(__('&larr; Manage All Fields', $this -> plugin_name), $this -> url); ?></div>
	<p><?php _e('Drag and drop the custom fields below to order them.', $this -> plugin_name); ?></p>

	<?php if (!empty($fields)) : ?>
		<div id="message" class="updated fade" style="width:30.8%; display:none;"></div>
		<div>
			<ul id="fields">
				<?php foreach ($fields as $field) : ?>
					<li id="fields_<?php echo $field -> id; ?>" class="newsletters_lineitem"><?php echo $field -> title; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		
		<script type="text/javascript">
		var request_fields = false;
		jQuery(document).ready(function() {
			jQuery("ul#fields").sortable({
				placeholder: 'newsletters-placeholder',
            	revert: 100,
            	distance: 5,
				start: function(event, ui) {
					if (request_fields) { request_fields.abort(); }
					jQuery('#message').slideUp();
				},
				update: function(event, ui) {
					jQuery.post(wpmlajaxurl + "?action=newsletters_order_fields", jQuery('ul#fields').sortable('serialize'), function(response) {
						jQuery('#message').html('<p>' + response + '</p>').fadeIn();
					});
				}
			});
		});
		</script>
	<?php else : ?>
		<p class="<?php echo $this -> pre; ?>error"><?php _e('No custom fields were found', $this -> name); ?></p>													
	<?php endif; ?>
</div>