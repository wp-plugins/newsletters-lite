<?php

/* This is a once-off file which is used upon installation to insert templates into the database */

$email_templates = array();

$email_templates['posts'] = array(
	'subject'					=>	false,
	'message'					=>	'<div class="wpmlposts">
	[post_loop]
		<div class="wpmlpost">
			<h3><a href="[post_link]" title="[post_title]">[post_title]</a></h3>
			[post_date_wrapper]<p><small>Posted on [post_date format="F jS, Y"] by [post_author]</small></p>[/post_date_wrapper]
			<div class="wpmlpost_content">
				[post_thumbnail]
				<p>[post_excerpt]</p>
			</div>
		</div>
		<hr style="visibility:hidden; clear:both;" />
	[/post_loop]
</div>',
);

$email_templates['latestposts'] = array(
	'subject'					=>	false,
	'message'					=>	'<div class="wpmlposts">
	[post_loop]
		<div class="wpmlpost">
			<h3><a href="[post_link]" title="[post_title]">[post_title]</a></h3>
			[post_date_wrapper]<p><small>Posted on [post_date format="F jS, Y"] by [post_author]</small></p>[/post_date_wrapper]
			<div class="wpmlpost_content">
				[post_thumbnail]
				<p>[post_excerpt]</p>
			</div>
		</div>
		<hr style="visibility:hidden; clear:both;" />
	[/post_loop]
</div>',
);

/* Subscriber confirmation email */
$email_templates['confirm'] = array(
	'subject'					=>	"Confirm Subscription",
	'message'					=>	"Good day,\r\n\r\nThank you for subscribing to the mailing list(s): [wpmlmailinglist].\r\nPlease click the link below to activate/confirm your subscription.\r\n\r\n[wpmlactivate]\r\n\r\nAll the best,\r\n[wpmlblogname]",
);

/* Bounce notification email to the administrator */
$email_templates['bounce'] = array(
	'subject'					=>	"Email Bounced",
	'message'					=>	"Good day,\r\n\r\nAn email has bounced.\r\nThe email address is: [wpmlemail].\r\nTotal bounces for this email/subscriber: [wpmlbouncecount].\r\n\r\nAll the best,\r\n[wpmlblogname]",
);

/* Unsubscribe notification to the administrator */
$email_templates['unsubscribe'] = array(
	'subject'					=>	"Unsubscription",
	'message'					=>	"Good day Administrator,\r\n\r\nA subscriber has unsubscribed from a mailing list.\r\nThe mailing list is: [wpmlmailinglist].\r\nThe subscriber email is: [wpmlemail].\r\n\r\n[wpmlunsubscribecomments]\r\n\r\nAll the best,\r\n[wpmlblogname]",
);

/* Expiration notification email to the subscriber */
$email_templates['expire'] = array(
	'subject'					=>	"Subscription Expired",
	'message'					=>	"Good Day,\r\n\r\nYour subscription has expired.\r\nThe mailing list is: [wpmlmailinglist].\r\nPlease click the link below to renew your subscription.\r\n\r\n[wpmlactivate]\r\n\r\nAll the best,\r\n[wpmlblogname]",
);

/* New order notification email sent to the administrator */
$email_templates['order'] = array(
	'subject'					=>	"Paid Subscription",
	'message'					=>	"Good day Administrator,\r\n\r\nYou have received a paid mailing list subscription order.\r\nThe mailing list is: [wpmlmailinglist].\r\nThe subscriber email is: [wpmlemail].\r\n\r\nAll the best,\r\n[wpmlblogname]",
);

/* Schedule notification email sent to the administrator */
$email_templates['schedule'] = array(
	'subject'					=>	"Email Cron Fired",
	'message'					=>	"Good day Administrator,\r\n\r\nYour email cron has been fired as scheduled.\r\n\r\nAll the best,\r\n[wpmlblogname]",
);

/* Subscribe notification email sent to the administrator */
$email_templates['subscribe'] = array(
	'subject'					=>	"New Subscription",
	'message'					=>	"Good day Administrator,\r\n\r\nA user/visitor has just subscribed to: [wpmlmailinglist].\r\nThe email address of this subscriber is: [wpmlemail].\r\n\r\n[wpmlcustomfields]\r\n\r\nAll the best,\r\n[wpmlblogname]",
);
	
?>