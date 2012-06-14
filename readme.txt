=== List Yo' Files ===
Plugin Name: List Yo' Files
Contributors: Wanderer LLC
Plugin URI: http://www.wandererllc.com/company/plugins/listyofiles/
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TC7MECF2DJHHY&lc=US
Tags: admin, files, MP3, mp3 player, music, music player, Audio Player, WPAudio, Flash, audio, embed, upload, download, FTP, display, list, show, ul, li, table, users, sub folders, membership, sermon, quotes, foreign, language
Requires at least: 2.8
Tested up to: 3.0
Stable tag: 1.12

== Description ==

Allows users to easily display lists of files in their pages and posts.  Supply the folder and various options and you can generate a list of files with hyperlinks to each file making it downloadable.  Extensive options let you sort and filter files.  You can include file size, date, and even an icon as part of the file list.  You can even display lists of MP3s in popular audio players.  The plugin admin pages also allow you to conveniently upload and delete files.  This is a easy way for organizations, groups, and clubs to share files with members.  For example, Home Owner Associations have used this plugin to list their minutes.  Music websites use this plugin to allow their users to show off their music.  

== Screenshots ==

1. The admin Upload Files UI provides a convenient place to upload files to a specified folder.
2. The admin Delete Files UI provides you a basic way to delete files in a given folder.  For a more powerful solution, use any popular FTP client.
3. Admins can manage their settings on this small page.  Enabling "User Folders" allows non-admin users to create their own user folders, upload files to them, and display them.
4. The non-admin Upload Files UI provides a simple way to create their own user folders and upload files to them.  Administrators can set restrictions on the number of folders, total size, and allowed file types.
5. The non-admin Delete Files UI provides a quick and easy way to delete files and folders.   

== Frequently Asked Questions ==

= What's the secret code for displaying a list of files in my pages or posts? =

To use, add the List Yo' Files shortcode ("listyofiles") enclosed in brackets to the text of your page or post and specify the folder name with the "folder" directive.  For example:  [listyofiles folder="wp-content/gallery/my-new-gallery"]  The plugin will then generate a list of files with a link to each so that every file is downloadable.  If you've enabled User Folders, non-admin users will have access to express shortcodes that allow them to display files with just a little typing.  See the plugin Usage page for extensive instructions.

= Is it possible to sort files, or restrict certain files from being displayed? =

It sure is.  To sort files, use the "sort" option.  To only display certain file types, use the "filter" option.  See the plugin Usage page for extensive instructions.

= Where can I find the complete FAQ? =

You can find the complete FAQ [here](http://www.faqme.com/listyofiles).

= How do I make suggestions or report bugs for this plugin? =

Just go to [to the List Yo' Files site](<http://www.wandererllc.com/company/plugins/listyofiles/>) and follow the instructions.

== Changelog ==

= 1.12 =

* French version available.
* File tables now use style sheets.  You can turn it off and on in the Administer page.

= 1.11 =

* Updated URL generation to handle UTF-8 paths.
* Bug fix for checking for existence of file icons.
* Added a Q&A to the FAQ, and fixed a mistake in the usage guide.
* Added a shortcode error message
* Suppressing warnings on opendir()
* Added some CSS placeholders for files in unordered list tags
* Prep for localization

= 1.10 =

* Using a different method for folder iteration to remove potential warnings when folder permissions aren't maxed out at 777.
* Added width alignment on icon column for table lists.
* Added a menu icon.

= 1.02 =

* Delete folder (user folders feature) bug fixed.

= 1.01 =

* Bug fix for user folders when no folder size restrictions were in place.
* Some minor cleanup.

= 1.00 =

* Added the concept of "User Folders", which is useful for sites that have members who want to manage their own set of files.  This is an extensive feature which enables new, simplified UI for non-admin users.
* Added support for displaying lists of mp3s in popular audio players.
* Improved the UI.

= 0.83 =

* Fixed the "icon" feature to be turned off by default.
* Icon extensions are now case insensitive.
* Using a method that does not generate a warning when a file cannot be loaded.
* Added icons for mp3, htm, and html.

= 0.82 =

* Added an "icon" feature which adds icons to file lists which are based on the "table" option.  Includes a small set of 16x16 icons.  Users can add their own icon files by uploading to the plugin's 'icon' folder a .png file with the file name matching the extension of the file you want to provide an icon for.  For example:  "mp3.png", "pdf.png", etc.

= 0.81 =

* Bug fix: Upload UI supports uploading 10 files at once.

= 0.8 =

* Added a "sort" option for sorting files.
* Added a "filter" option for filtering files based on file extension.
* Added an "options" option for supporting special options in your list like tables and extra fields.
* Upload feature is available to Authors and up.  Delete feature is available to Editors and up.
* Each file list now has a unique ID.
* Leaving "folder" argument undeclared now produces a text warning instead of listing the WordPress root folder.

== Upgrade Notice ==

= 1.12 =

If you want to style your file tables or have a French site, then update.

= 1.11 =

This is a minor update, but is recommended for everyone.

= 1.10 =

Minor upgrade which eliminates potential warnings when folder permissions aren't optimal.

= 1.02 =

Recommended for users who use the "user folders" feature.

= 1.01 =

Recommended for users who use the "user folders" feature.

= 1.00 =

Recommended for all users.  Greatly expands the plugin's feature set.

= 0.83 =

Recommended for users of 0.82.  Fixes non-critical bugs.

= 0.82 =

Non-critical update for users who want to have a file icon in front of their file.

= 0.81 =

Recommended for all users.  Fixed the Uploads page so that 10 files can be uploaded at once.

= 0.8 =

Recommended for all users.  Several feature updates plus fixed this issue of displaying WordPress's root files if no "folder" argument is specified.

== Acknowledgments ==

There are many people who have suggested features for List Yo' Files.  Special consideration needs to be made to the following people who had an active role in contributing by providing a detailed design, monetary sponsorship, or offering to test and provide useful feedback:

* [Underground Music Nation](http://www.undergroundmusicnation.com/) for supporting the User Folders and MP3 Player Integration features.
* [MKKH Marketing](http://mkkhmarketing.com/) and Matthew Hart for excellent testing on the User Folders upload and delete features.
* [Christ Church of Conroe](http://christchurchconroe.org/) and Ron Frasier for good testing on the upload feature.
* [Peter Liu](http://liuhui998.com) for UTF-8 encoding suggestions. 
* Eli Webster for suggestions on detecting icon files.
* [Li-An](http://www.li-an.fr/wpplugins/) for the French translation.
* [Matthew Mower](http://web.missouri.edu/~vignaleg/?page_id=159) for his suggestions on CSS styling.
* Roberta Bremner for discovering a missing <tr> tag and providing the fix.

== License ==

This file is part of List Yo' Files.

List Yo' Files is free software:  you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.  

List Yo' Files is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

See the license at <http://www.gnu.org/licenses/>.
