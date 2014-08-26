<table class="form-table">
	<tbody>
		<tr>
			<th><label for="sendattachmentN"><?php _e('Send Attachment(s)', $this -> plugin_name); ?></label></th>
			<td>
				<label><input <?php echo (!empty($_POST['attachments'])) ? 'checked="checked"' : ''; ?> onclick="jQuery('#attachmentdivinside').show();" type="radio" name="sendattachment" value="Y" /> <?php _e('Yes', $this -> plugin_name); ?></label>
				<label><input <?php echo (empty($_POST['attachments'])) ? 'checked="checked"' : ''; ?> onclick="jQuery('#attachmentdivinside').hide();" type="radio" name="sendattachment" id="sendattachmentN" value="N" /> <?php _e('No', $this -> plugin_name); ?></label>
                <span class="howto"><?php _e('You can attach files to this email for your subscribers to receive.', $this -> plugin_name); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<div id="attachmentdivinside" style="display:<?php echo (!empty($_POST['attachments'])) ? 'block' : 'none'; ?>;">
	
    <table class="form-table">
    	<tbody>
            <tr>
            	<th><label for="addattachment"><?php _e('Attachments', $this -> plugin_name); ?></label></th>
                <td>
                	<?php if (!empty($_POST['attachments'])) : ?>
                        <div id="currentattachments">
                           <ul style="margin:0; padding:0;"> 
                                <?php foreach ($_POST['attachments'] as $attachment) : ?>
                                	<li class="<?php echo $this -> pre; ?>attachment">
                                    	<?php echo $Html -> attachment_link($attachment['filename'], false); ?>
                                        <a href="?page=<?php echo $this -> sections -> history; ?>&amp;method=removeattachment&amp;id=<?php echo $attachment['id']; ?>" onclick="if (!confirm('<?php _e('Are you sure you want to remove this attachment?', $this -> plugin_name); ?>')) { return false; }"><img border="0" style="border:none;" src="<?php echo $this -> url(); ?>/images/icons/delete-16.png" alt="delete" /></a>
                                    </li>    
                                <?php endforeach; ?>
                           </ul>
                        </div>
                    <?php endif; ?>
                
                	<div id="newattachments"></div>
                    
                    <h4><a href="" id="addattachment" class="button button-secondary" onclick="add_attachment(); return false;"><?php _e('Add an attachment', $this -> plugin_name); ?></a></h4>
                </td>
            </tr>
        </tbody>
    </table>
    
    <script type="text/javascript">
	var count = 1;
	
	function delete_attachment(countid) {
		jQuery('#newattachment' + countid).remove();
	}
	
	function add_attachment() {
		var atthtml = "";
		atthtml += '<div class="newattachment" id="newattachment' + count + '" style="display:none;">';
		atthtml += '<input type="file" name="attachments[]" value="" />';
		atthtml += ' <a class="button button-secondary button-small" href="" onclick="if (confirm(\'<?php _e('Are you sure you want to remove this?', $this -> plugin_name); ?>\')) { delete_attachment(' + count + '); } return false;"><?php _e('Remove'); ?></a>';
		atthtml += '</div>';
		
		jQuery('#newattachments').append(atthtml);
		jQuery('#newattachment' + count).fadeIn();
		count++;	
	}
	
	function delete_current_attachment(attachmentid) {
			
	}
	</script>
</div>