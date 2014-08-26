<h3><?php _e('Other Available Lists', $this -> plugin_name); ?></h3>
<p><?php _e('You can subscribe to our other mailing list(s) as well.', $this -> plugin_name); ?></p>

<?php if (!empty($success) && $success == true) : ?>
	<p class="<?php echo $this -> pre; ?>error"><?php echo $successmessage; ?></p>
<?php endif; ?>

<?php $this -> render('error', array('errors' => $errors), true, 'default'); ?>

<?php if (!empty($otherlists)) : ?>
    <table>
        <tbody>
            <?php foreach ($otherlists as $list_id) : ?>
                <?php $Db -> model = $Mailinglist -> model; ?>
                <?php if ($mailinglist = $Db -> find(array('id' => $list_id))) : ?>
                    <tr>
                        <td>
							<?php echo $mailinglist -> title; ?>
                            <?php if ($mailinglist -> paid == "Y") : ?>
                            	<?php $intervals = $this -> get_option('intervals'); ?>
                            	<span class="wpmlcustomfieldcaption"><small>(<?php echo $Html -> currency() . '' . number_format($mailinglist -> price, 2, '.', '') . ' ' . $intervals[$mailinglist -> interval]; ?>)</small></span>
                            <?php endif; ?>
                        </td>
                        <td><span id="subscribenowlink<?php echo $list_id; ?>"><a href="javascript:wpmlmanagement_subscribe('<?php echo $subscriber -> id; ?>', '<?php echo $list_id; ?>');" class="<?php echo $this -> pre; ?>button subscribebutton"><?php _e('Subscribe Now', $this -> plugin_name); ?></a></span></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <script type="text/javascript">jQuery(document).ready(function() { jQuery('.<?php echo $this -> pre; ?>button').button(); });</script>
<?php else : ?>
	<p class="<?php echo $this -> pre; ?>error"><?php _e('No other subscriptions are available', $this -> plugin_name); ?></p>
<?php endif; ?>