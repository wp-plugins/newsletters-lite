= Newsletter plugin languages =

The language files of the Newsletter plugin are separately distributed.
There are two reasons for this:

1. To reduce the size of the plugin which needs to be installed/uploaded
2. To allow you to host the languages in an external folder to prevent it from being overwritten.

== How to obtain the language files ==

You can obtain the language files from Github at: https://github.com/tribulant/wp-mailinglist-languages
This folder contains all the .PO and .MO files with the current, available languages.

Only the .MO files are actually used by WordPress. 
The .PO files are the source, which can be opened with an application such as poEdit: http://www.poedit.net

== How to use the language files ==

Follow these steps to use the language file(s) you want:

1. Pick the .MO files from Github for the languages that you want to use. For example "wp-mailinglist-de_DE.mo"
2. Go to "wp-content/plugins/" in your WordPress installation and create a "wp-mailinglist-languages" folder there.
3. Upload the "wp-mailinglist-de_DE.mo" file to "wp-content/plugins/languages/" folder you created.
4. Go to Newsletters > Configuration > System > WordPress Related in the plugin and tick/check "Yes, load external language file" for the "Load External Language" setting.

Please note, if you don't tick/check that setting in #4 above, the plugin will look inside "wp-content/plugins/wp-mailinglist/languages/" for a language file.

That's it, the German - de_DE (in this example) language file will now be loaded if the locale is de_DE.
In the same manner, you can add more .MO files to that "wp-content/plugins/wp-mailinglist-languages/" folder accordingly.

= How to update the language files =

Follow these steps to update language files:

1. Open the "wp-mailinglist-languages" folder distributed with the plugin.
2. Pick the .PO file for the language that you want to update for example "wp-mailinglist-es_ES.po" which is Spanish/Espanol.
3. Open the .PO file in an application like poEdit: http://www.poedit.net
4. Check the settings of the catalog in the application so it scans the correct paths, etc... and run a scan/update on the .PO file.
5. Translate/update strings as needed and then save the .PO file. It will automatically generate a new .MO file which can be used with the plugin and WordPress

For any questions or problems, please contact us: http://tribulant.com/support/