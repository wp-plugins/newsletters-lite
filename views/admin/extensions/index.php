<div class="wrap <?php echo $this -> pre; ?>">
	<h2><?php _e('Manage Extensions', $this -> plugin_name); ?></h2>
    <?php $this -> render('extensions' . DS . 'navigation', false, true, 'admin'); ?>
    <p><?php _e('These are extensions which extend the functionality of the Newsletter plugin.', $this -> plugin_name); ?></p>
    
    <?php if (!empty($this -> extensions)) : ?>
    	<table class="widefat">
        	<thead>
            	<tr>
                	<th colspan="2"><?php _e('Extension Name', $this -> plugin_name); ?></th>
                    <th><?php _e('Extension Status', $this -> plugin_name); ?></th>
                </tr>
            </thead>
            <tfoot>
            	<tr>
                	<th colspan="2"><?php _e('Extension Name', $this -> plugin_name); ?></th>
                    <th><?php _e('Extension Status', $this -> plugin_name); ?></th>
                </tr>
            </tfoot>
        	<tbody>
            	<?php $class = ''; ?>
            	<?php foreach ($this -> extensions as $extension) : ?>                
                	<?php
					
					if ($this -> is_plugin_active($extension['slug'], false)) {
						$status = 2;	
					} elseif ($this -> is_plugin_active($extension['slug'], true)) {
						$status = 1;
					} else {
						$status = 0;
					}
					
					$context = 'all';
					$s = '';
					$page = 1;
					$path = $extension['plugin_name'] . DS . $extension['plugin_file'];
					$img = (empty($extension['image'])) ? $this -> url() . '/images/extensions/' . $extension['slug'] . '.png' : $extension['image'];
					
					?>
                
                	<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                		<th style="width:85px;">
                			<a onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', href:'<?php echo $extension['link']; ?>'}); return false;" href="<?php echo $extension['link']; ?>" title="<?php echo esc_attr($extension['name']); ?>" style="border:none;">
                				<img class="extensionicon" style="border:none; width:75px; height:75px;" border="0" src="<?php echo $img; ?>" alt="<?php echo $extension['slug']; ?>" />
                			</a>
                		</th>
                    	<th>
							<a onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', href:'<?php echo $extension['link']; ?>'}); return false;" href="<?php echo $extension['link']; ?>" title="<?php echo esc_attr($extension['name']); ?>" class="row-title"><?php echo $extension['name']; ?></a>
							<br/><small class="howto"><?php echo $extension['description']; ?></small>
                            <div class="row-actions">
                            	<?php 
								
								switch ($status) {
									case 0	:
										?>
                                        
                                        <span class="edit"><a onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', href:'<?php echo $extension['link']; ?>'}); return false;" href="<?php echo $extension['link']; ?>" target="_blank"><?php _e('Get this extension now', $this -> plugin_name); ?></a></span>
                                        
                                        <?php
										break;
									case 1	:
										?>
                                        
                                        <span class="edit"><?php echo $Html -> link(__('Activate', $this -> plugin_name), wp_nonce_url('?page=' . $this -> sections -> extensions . '&method=activate&plugin=' . plugin_basename($path))); ?></span>
                                        
                                        <?php
										break;
									case 2	:
										?>
                                        
                                        <span class="delete"><?php echo $Html -> link(__('Deactivate', $this -> plugin_name), wp_nonce_url('?page=' . $this -> sections -> extensions . '&method=deactivate&plugin=' . plugin_basename($path)), array("onclick" => "if (!confirm('" . __('Are you sure you want to deactivate this extension?', $this -> plugin_name) . "')) { return false; }", 'class' => "submitdelete")); ?></span>
                                        
                                        <?php
										break;	
								}
								
								if (!empty($extension['settings'])) {
									?>| <span class="edit"><?php echo $Html -> link(__('Settings', $this -> plugin_name), $extension['settings']); ?></span><?php
								}
								
								?>
                            </div>
                        </th>
                        <th>
                        	<?php 
							
							switch ($status) {
								case 0			:
									?><span class="<?php echo $this -> pre; ?>error"><?php _e('Not Installed', $this -> plugin_name); ?></span> <small>(<?php echo $Html -> link(__('Buy Now', $this -> plugin_name), $extension['link'], array('target' => "_blank", 'onclick' => "jQuery.colorbox({iframe:true, width:'80%', height:'80%', href:'" . $extension['link'] . "'}); return false;")); ?>)</small><?php
									break;
								case 1			:
									?><span class="<?php echo $this -> pre; ?>error"><?php _e('Installed but Inactive', $this -> plugin_name); ?></span><?php
									break;
								case 2			:
									?><span class="<?php echo $this -> pre; ?>success"><?php _e('Installed and Active', $this -> plugin_name); ?></span><?php
									break;	
							}
							
							?>
                        </th>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
    	<p class="<?php echo $this -> pre; ?>error"><?php _e('No extensions found.', $this -> plugin_name); ?></p>
    <?php endif; ?>
</div>