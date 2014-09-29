=== Tribulant Newsletters ===
Contributors: contrid
Donate link: http://tribulant.com
Tags: newsletters, email, bulk email, mailing list, subscribers, newsletter, optin, subscribe, marketing, auto newsletter, automatic newsletter, autoresponder, campaign, email, email alerts, email subscription, emailing, follow up, newsletter signup, newsletter widget, newsletters, post notification, subscription, bounce, latest posts, insert posts into newsletter
Requires at least: 3.8
Tested up to: 4.0
Stable tag: 4.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html 

Newsletter plugin for WordPress to capture subscribers and send beautiful, bulk newsletter emails.

== Description ==

A full-featured WordPress newsletter plugin created by <a href="http://tribulant.com">Tribulant Software</a> for WordPress which fulfils all subscribers, emails, marketing and newsletter related needs for both personal and business environments.

It has robust, efficient and unique features! This is an all-in-one newsletter tool for your WordPress site can be configured to behave as desired and it will provide the best experience for your email subscribers at the same time.

The software works the way you do so you can focus on creating newsletters and giving your website the necessary exposure!

= Features =

Some of the features in the WordPress Newsletter plugin include:

* Multiple Mailing Lists 
* Bounce Email Management 
* Newsletter Queue & Scheduling 
* Newsletter Templates 
* Complete Email History 
* Unlimited Sidebar Widgets 
* Post/Page Opt-In Embedding 
* Offsite Subscription Forms 
* Publish Newsletter as a Post 
* Send Post as a Newsletter 
* Add Email Attachments 
* SMTP Authentication 
* Ajax Powered Features 
* Import/Export Subscribers 
* Paid Subscriptions (PayPal & 2CheckOut) 
* Integrates with the banner rotator plugin 
* WordPress Multi-Site Compatible
* Email Tracking 
* IP Logging of Subscribers
* Newsletter Themes 
* POP/IMAP Bounce Handling 
* Latest Posts Subscription
* Single/Multiple Posts into Emails 
* Bit.ly click tracking 
* Autoresponders 
* Newsletters by conditions 
* Multilingual (qTranslate & WPML) 
* Custom Post Types 
* Link/click tracking 
* DKIM Signature 
* WordPress Dashboard Widget
* and much more...

= Demo and Support =

See the <a href="http://tribulant.net/newsletter/">online demonstration</a> and view the <a href="http://docs.tribulant.com/wordpress-mailing-list-plugin/31">online documentation</a> for tips, tricks, guides and more.

= Extensions =

There are many free and paid extension plugins for the WordPress Newsletter plugin. All extensions work with both Newsletters LITE and Newsletters PRO, no problem.

Some extensions include:

* <a href="http://tribulant.com/extensions/view/42/woocommerce-subscribers">WooCommerce Subscribers</a>
* <a href="http://tribulant.com/extensions/view/28/contact-form-7-subscribers">Contact Form 7 Subscribers</a>
* <a href="http://tribulant.com/extensions/view/46/google-analytics">Google Analytics Tracking</a>
* <a href="http://tribulant.com/extensions/view/6/embedded-images">Embedded Images</a>
* <a href="http://tribulant.com/extensions/view/26/total-ms-control">Total MS Control</a>
* <a href="http://tribulant.com/extensions/view/17/gravity-forms-subscribers">Gravity Forms Subscribers</a>
* <a href="http://tribulant.com/extensions/view/16/formidable-subscribers">Formidable Subscribers</a>
* <a href="http://tribulant.com/extensions/view/43/digital-access-pass">Digital Access Pass Subscribers</a>
* <a href="http://tribulant.com/extensions/view/36/total-control">Total Control</a>
* <a href="http://tribulant.com/extensions/view/32/s2member-subscribers">s2Member Subscribers</a>
* <a href="http://tribulant.com/extensions/view/31/wp-emember-subscribers">WP eMember Subscribers</a>

<a href="http://tribulant.com/plugins/extensions/1/wordpress-newsletter-plugin">Visit the Newsletters extensions page</a>

= PRO Version =

The Newsletters LITE version has all the features that the PRO version has but it has some limitations.

You can have one mailing list, 500 subscribers, send 1000 emails per month and the custom dynamic fields are not available. These limits should be sufficient for a personal blogger or a small business.

To remove these limits, you can upgrade to the PRO version and submit your serial key inside the plugin.

In addition to the limits being removed, you will receive <a href="http://tribulant.com/support/">priority support</a> from <a href="http://tribulant.com">Tribulant Software</a>.

<a href="http://tribulant.com/plugins/view/1/wordpress-newsletter-plugin">Visit the Newsletters PRO page</a>

== Installation ==

Installing the WordPress Newsletter plugin is simple. Follow these steps:

= Automatic Installation =

1. Go to **Plugins > Add New** in your WordPress dashboard.
1. Search `newsletters` to find this plugin, by Tribulant Software.
1. Click **Install Now** to install it and then activate it after the installation.

= Manual Installation =

1. Extract the `zip` file to obtain the plugin folder.
1. Upload the plugin folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the **Plugins** menu in WordPress

== Screenshots ==

1. Premade newsletter themes included
2. Detailed statistics for emails, subscribers, etc.
3. Flexible configuration settings
4. Easy, WYSIWYG newsletter creation
5. Complete history of newsletters with stats
6. Import subscribers from CSV or Mac OS X vCard
7. Export subscribers to CSV file
8. Email queue with scheduling
9. Many extensions and integrations available
10. Dashboard widget for quick overview

== Changelog ==

= 4.4 =
* ADD: Filters in the email queue section
* ADD: See queued emails count when viewing sent/draft emails individually
* ADD: Prevent autoresponder creationg when manually adding subscribers in admin
* ADD: Checkbox to specify Ajax queuing/sending progress while creating newsletter
* ADD: See all unsubscribes history
* ADD: Built-in, daily cron to optimize the database tables
* ADD: Button to open latest posts preview in a new window/tab
* ADD: Export delimiter setting for CSV
* ADD: Empty index.php file in plugin folder to prevent indexing of files
* ADD: Insert anchor links with "name" attribute from TinyMCE
* ADD: Custom hidden field that takes any value, editable by admin
* ADD: Archive sent emails older than X days to a flat file
* ADD: "Continue editing" checkbox in all save sections of admin
* ADD: Show Unsubscribe users to delete
* ADD: Display attached files from newsletter on published post  
* IMPROVE: More action/filter hooks
* IMPROVE: Improved publish newsletter as post behaviour
* IMPROVE: Prioritise emails in the queue without an existing error on them
* IMPROVE: Check custom fields on subscribers table when clicking "Check/optimize database"
* IMPROVE: MySQL optimization of tables with indexes
* IMPROVE: Tabs under Import/Export section in admin for easier navigation
* IMPROVE: Queue, import, etc... performance improvements
* IMPROVE: Columns in Newsletters > Subscribers section should reflect in order of custom fields
* IMPROVE: Improved help tooltips design
* IMPROVE: View/Edit buttons per history email under Newsletters > Overview section
* IMPROVE: Change 'init' hook priority to 11 for compatibility
* IMPROVE: Change dashboard widget stats chart/graph to 14 days to prevent clutter
* IMPROVE: "Edit" button in dashboard widget latest emails
* IMPROVE: Improvements when upgrading from very old version
* IMPROVE: New, improved TinyMCE button and interfaces
* IMPROVE: Remove unused font files
* IMPROVE: Hidden custom fields should be editable by admin
* IMPROVE: Move bounce "Server Type" setting into CGI bounce DIV
* IMPROVE: Only fire wp_schecule_single_event once, on first activation
* IMPROVE: Put background:none; on offsite code iframe
* IMPROVE: Mark email as read if a user clicks a link inside it  
* FIX: Ajax progress queuing/sending to users broken
* FIX: Clicks section bulk actions not working
* FIX: "Send Now" link to user from the email queue doesn't work
* FIX: Database error when queuing to only users, no mailing lists
* FIX: Cannot turn off captcha on hardcoded forms
* FIX: Language files don't load with different plugin folder name
* FIX: Post excerpt shortcode no longer working
* FIX: Export subscribers to CSV generates HTML code at the end of the file
* FIX: Subscriber validation fails on special characters like é
* FIX: Associated records not deleted when parent is deleted
* FIX: Newsletters lite admin bar menu shows up on multi-site
* FIX: "Delete Subscriber on Unsubscribe" not working as it should.  

= 4.3.9 =
* ADD: WordPress 4.0 compatibility
* ADD: More action/filter hooks in the core
* ADD: Hidden custom fields  
* IMPROVE: get_editable_user_ids() deprecated
* IMPROVE: Improvements to the newsletters RSS feed
* IMPROVE: Change $user_ID checks to is_user_logged_in()
* IMPROVE: Don't show the "RSS" button under Sent & Draft Emails if RSS is turned off
* IMPROVE: Change 'init' hook priority
* IMPROVE: 'list' parameter for the [newsletters_subscriberscount] shortcode   
* FIX: Subscriber exists redirect doesn't work on "all" mailinglists subscribe form
* FIX: Latest posts send with no posts and group by category
* FIX: Manage subscriptions incorrectly says "You are logged in..."
* FIX: Manage subscriptions unsubscribe "You are not subscribed to any lists."
* FIX: PHP Fatal error: Call to undefined function icl_get_languages()  

= 4.3.8 =
* ADD: New meta box under Configuration > System for permissions
* ADD: Setting to include theme when publishing post from newsletter
* ADD: Roles permissions on certain blocks eg. "Send to roles" checkboxes list
* ADD: Subscribe to all lists by default without showing checkboxes
* ADD: Setting to choose in which order queued emails go out
* ADD: Permalink/shortlink to online version of newsletter
* ADD: Upload/specify image/logo for the tracking image
* IMPROVE: Links to remove send/system default from newsletter themes
* IMPROVE: WPML (multilingual) dashboard language switcher odd behaviour
* IMPROVE: Remove tracking image ALT attribute  
* FIX: Dashboard widget latest subjects not showing due to CSS
* FIX: Inserting multiple posts 'post_type' parameter/attribute empty
* FIX: Role permissions reset back to just 'administrator'
* FIX: Can't pick a group in mailing lists set group bulk action
* FIX: Can't send a Newsletter only to WP Users
* FIX: Multilingual posts shortcode links go to home page
* FIX: Bounce count not updating correctly  

= 4.3.7 =
* ADD: NEWSLETTERS_NAME constant to define the plugin/folder name
* ADD: Mandatory subscribers that cannot unsubscribe
* ADD: Open live preview in new window when creating a newsletter  
* IMPROVE: Remove wp-mailinglist-ajax.php completely and replace with wp_ajax_
* IMPROVE: Remove TimThumb image script
* IMPROVE: Remove included scripts and replace them with WordPress scripts
* IMPROVE: Only show update notification to users who can 'edit_plugins'
* IMPROVE: Button to hide update notification  
* FIX: Latest Post subscriptions not logging posts with group by category setting on
* FIX: Some permissions is not showing up in the list.
* FIX: Multilingual - Checkbox list in sidebar widget not working
* FIX: Editing future scheduled post unselects mailing lists
* FIX: "Screen Options" custom fields not effective immediately
* FIX: Export history and history emails breaks paging and link
* FIX: Clicking on all languages in pages gives memory issue- fatal error
* FIX: Multilingual - Specific list in widget shows drop down anyways
* FIX: Subscribers filters resetting when paging is clicked
* FIX: 'showdate' parameter ineffective on multiple posts shortcode
* FIX: Dollar sign ($) like price in subject parses as PHP variable and disappears
* FIX: Broken HTML on shortcode subscribe form due to 3rd party plugins  

= 4.3.6.2 =
* ADD: Screenshots for WordPress.org plugin page
* IMPROVE: Updated readme.txt file
* FIX: Fixed some paths, dynamic folder name

= 4.3.6.1 =

* Initial release/commit to WordPress.org plugins directory
* See the previous <a href="http://docs.tribulant.com/wordpress-mailing-list-plugin/31#doc5">release notes</a> in our docs.