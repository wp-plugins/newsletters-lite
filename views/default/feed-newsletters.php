<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title><?php echo get_bloginfo('name'); ?> <?php _e('Newsletters', $this -> plugin_name); ?></title>
		<link><?php echo home_url(); ?></link>
		<description><?php echo get_bloginfo('description'); ?></description>
		<lastBuildDate><?php echo $Html -> gen_date("r", time()); ?></lastBuildDate>
		
		<?php if (!empty($emails)) : ?>
			<?php foreach ($emails as $email) : ?>
				<item>
					<title><?php echo esc_attr(stripslashes($email -> subject)); ?></title>
					<link><?php echo home_url(); ?>/?<?php echo $this -> pre; ?>method=newsletter&amp;id=<?php echo $email -> id; ?>&amp;fromfeed=1</link>
					<guid><?php echo home_url(); ?>/?<?php echo $this -> pre; ?>method=newsletter&amp;id=<?php echo $email -> id; ?>&amp;fromfeed=1</guid>
					<pubDate><?php echo $Html -> gen_date("r", strtotime($email -> modified)); ?></pubDate>
					<description><![CDATA[ <?php echo esc_attr(strip_tags(apply_filters('the_content', $this -> strip_set_variables($email -> message)))); ?> ]]></description>
				</item>
			<?php endforeach; ?>
		<?php endif; ?>
	</channel>
</rss>