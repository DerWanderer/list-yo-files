<div class="wrap" style="max-width:950px !important;">
<h2><?php _e('Usage Guide', LYF_DOMAIN); ?></h2>
<div id="poststuff" style="margin-top:10px;">
<div id="mainblock" style="width:710px">
<div class="dbx-content">
<?php
wp_nonce_field('filez-nonce');
$pluginFolder = get_bloginfo('wpurl') . '/wp-content/plugins/' . dirname( plugin_basename( __FILE__ ) ) . '/';

// Variable to see if subscriber folders is turned ON
$enableUserFolders = get_option( LYF_ENABLE_USER_FOLDERS );

// Show simplified help?
$enableSimpleHelp = get_option( LYF_ENABLE_SIMPLE_HELP );

// Get the user's info
global $current_user;
get_currentuserinfo();

if ( "on" == $enableUserFolders && !current_user_can( 'delete_users' ) )
{
	//
	//	START:  UI for non-admins
	//

	// First, make sure their user folder exists.  If it doesn't then create it
	$userFolder = LYFGetUserUploadFolder( TRUE );

	if ( !is_dir( $userFolder ) )
	{
		$result = LYFCreateUserFolder( $userFolder );

		if ( $result < 0 )
		{
			global $current_user;
			get_currentuserinfo();

			$message = '<div id="message" class="updated fade"><p>' . __('Problem creating your user folder! ', LYF_DOMAIN );
			$message .= LYFConvertError( $result, $current_user->user_login );
			$message .= '</p></div>';
			echo $message;
		}
	}
	// Generate the list of user folders - conveniently show the user easy shortcode
	// customized for them.
	$folders = LYFGenerateFolderList( $userFolder );
?>

<div class="postbox">
	<h3 class="hndle"><span>Displaying File Lists:</span></h3>
	<div class="inside">
<?php
if ( "on" === $enableSimpleHelp )
{
?>
		<p>Here are the codes that you can use in your posts or pages to display your files.  Just copy <strong>one</strong> of the
		lines of text in the right-hand column and paste it into your the post or page that you want it to appear.</p>
		<p>"showmp3s" is an easy way to display your mp3 files.  "showfiles" is a more general-purpose way of displaying
		all types of files.</p>

		<?php
		if ( 0 == count( $folders ) )
		{
		?>

		<p><em>You have no folders that you can display. Read 'Creating Folders and Uploading Files' first.</em></p>

		<?php
		}
		else
		{
		?>
		<div id='filelist'>
		<table class="widefat" style="width:680px">
		<thead>
		  <tr>
		    <th scope="col">Folder name:</th>
		    <th scope="col">Code that you can add to your page or post (include the brackets "[]"):</th>
		  </tr>
		  </thead>
		  <?php
		  	// Get info on the user (used for assembling the folder name)
			global $current_user;
			get_currentuserinfo();

			// Loop through each sub folder
			foreach( $folders as $folder )
			{
				$mp3sText = LYFGetMP3Code( $current_user->user_login, $folder );
				$showFilesText = LYFShowFilesCode( $current_user->user_login, $folder );
				$finalText = $mp3sText . '<br />' . $showFilesText;

				// print an option for each folder
				print '<tr class="alternate"><td>'.$folder.'</td><td>'.$finalText.'</td></tr>';
			}
		  ?>
		</table>
		</div>
		<?php
		}
		?>
		<p><strong>NOTE: </strong>You can add 'options="download"' to your "showmp3s" code to include a download link to the file.</p>
		<p>You can try removing various <em>options</em> from the "showfiles" code above to further customize your lists.
		You can also include the following (none are required):</p>
			<fieldset style="margin-left: 20px;">
			1. <em>sort</em> - include one of the following:  "alphabetic", "reverse_alphabetic", "filesize",
			"reverse_filesize", "date", or "reverse_date".  The <em>default</em> is "alphabetic" and
			is used if "sort" isn't specified.  Example usage:
			<small>[showfiles folder='<?php echo $current_user->user_login;?>/<?php echo $folders[0]?>' sort='reverse_filesize']</small><br /><br />

			2. <em>filter</em> - include a list of extensions (no period) separated by commas to only display
			matching files.  For example,
			<small>[showfiles folder='<?php echo $current_user->user_login;?>/<?php echo $folders[0]?>' filter='pdf,doc,txt']</small> will
			only display audio files in your file list.  Not including this option will list all
			files in the specified folder.<br /><br />
			</fieldset>
<?php
}
else
{
?>
		<p>You have the following options for displaying your files:</p>

		<fieldset style="margin-left: 20px;">
		<p>1) To display a list of files, add the special <em>showfiles</em> code
		to the page or post where you want to display the files.  Be sure to include
		the folder to display.  For example:
		<small>[showfiles folder='<?php echo $current_user->user_login;?>/SUBFOLDER_NAME']</small>.

		You can customize your list with the following (none are required):</p>
			<fieldset style="margin-left: 20px;">
			1. <em>sort</em> - include one of the following:  "alphabetic", "reverse_alphabetic", "filesize",
			"reverse_filesize", "date", or "reverse_date".  The <em>default</em> is "alphabetic" and
			is used if "sort" isn't specified.  Example usage:
			<small>[showfiles folder='<?php echo $current_user->user_login;?>/SUBFOLDER_NAME' sort='reverse_filesize']</small><br /><br />

			2. <em>filter</em> - include a list of extensions (no period) separated by commas to only display
			matching files.  For example,
			<small>[showfiles folder='<?php echo $current_user->user_login;?>/SUBFOLDER_NAME' filter = 'mp3,wav,aif']</small> will
			only display audio files in your file list.  Not including this option will list all
			files in the specified folder.<br /><br />

			3. <em>options</em> - A list of comma-separated options to further customize your file list.  An example: <small>[showfiles folder='<?php echo $current_user->user_login;?>/SUBFOLDER_NAME' options='table,filesize,icon']</small> Supported options:
			<fieldset style="margin-left: 20px;">
				<br>a. <em>table</em> - Renders your file list as a table (no border, though your CSS may override this).</br>
				<br>b. <em>filesize</em> - Includes the file size in the list.</br>
				<br>c. <em>date</em> - Includes the file modified date in the list.</br>
				<br>d. <em>new_window</em> - Will open links in a new window.</br>
				<br>e. <em>hide_extension</em> - Hides file extensions.</br>
				<br>f. <em>icon</em> - Works only with the <em>table</em> option.  This option displays a file
				icon to the left of the filename.</br>
				<br>g. <em>wpaudio</em> - <em>Requires the <a href="http://wordpress.org/extend/plugins/wpaudio-mp3-player/" target="_blank">WPAudio plugin</a></em>.  This option transforms the filename from a download link into the WPAudio mp3 player.  <strong>Note:</strong> Use a filter to restrict files to mp3s.</br>
				<br>h. <em>wpaudiodownloadable</em> - <em>Requires the <a href="http://wordpress.org/extend/plugins/wpaudio-mp3-player/" target="_blank">WPAudio plugin</a></em>.  This option only works if <em>wpaudio</em> is also specified.  It adds a download link for the song.</br>
				<br>i. <em>audioplayer</em> - <em>Requires the <a href="http://wordpress.org/extend/plugins/audio-player/" target="_blank">Audio Player plugin</a></em>. This option transforms the filename from a download link into the AudioPlayer mp3 player.  <strong>Note:</strong> Use a filter to restrict files to mp3s. If you specify <em>table</em>, then each song will display in an individual player.  Otherwise, the songs will appear as a group in a single player.</br>
			</fieldset>
			<p />
			</fieldset>
		</fieldset>

		<fieldset style="margin-left: 20px;">
		<p>2) To display a list of playable mp3s, add the special <em>showmp3s</em> code
		to the page or post where you want to display the files. For example:
		<small>[showmp3s folder="<?php echo $current_user->user_login;?>/SUBFOLDER_NAME"]</small>.
		<em>NOTE:</em> You do not need to supply 'options' when you use 'showmp3s'.  However, if you
		want the file to be downloadable, include this: 'options="download"'. For example:
		<small>[showmp3s folder="<?php echo $current_user->user_login;?>/SUBFOLDER_NAME' options="download"]</small>
		</p>
		</fieldset>

		<p><em>NOTE:</em> Do not add opening or closing slashes ("/") to the "folder" path.</p>

<?php
}
?>

	</div>
</div>

<div class="postbox">
	<h3 class="hndle"><span>Creating Folders and Uploading Files:</span></h3>
	<div class="inside">
		<p>Before you upload any files, you need to create at least one subfolder to upload your files to.
		Follow these steps:</p>
		<fieldset style="margin-left: 20px;">
			1. Type the name of a folder.  Choose a short name that's easy to recognize.  <em>Note</em>: The folder name is never displayed to users.<br />
			<br>2. Click on "Create Folder" to create the folder.</br>
		</fieldset>
		<p>Once you have at least one folder, you can upload files.  Here are the steps:</p>
		<fieldset style="margin-left: 20px;">
			1. Choose a folder from the select box that you want to upload files to.<br />
			<br>2. Use the "Browse" button to select files to upload.  You may upload up to ten at once.</br><br />
			<br>3. Click on "Upload Files".</br>
		</fieldset>
	</div>
</div>

<div class="postbox">
	<h3 class="hndle"><span>Deleting Folders and Files:</span></h3>
	<div class="inside">
		<p>To delete a folder, including all of its contents, follow these steps:</p>
		<fieldset style="margin-left: 20px;">
			1. Select the folder you want to delete.<br />
			<br>3. Click on the "Delete Folder" button.</br>
		</fieldset>
		<p />
		<p>To delete individual files, follow these steps:</p>
		<fieldset style="margin-left: 20px;">
			1. Select the folder you want to browse.<br />
			<br>2. Click the "List Files" button.</br><br />
			<br>3. Selectively click on "Delete" links to delete files.</br>
		</fieldset>
	</div>
</div>

<?php
}
else
{
?>

<div class="postbox">
	<h3 class="hndle"><span><?php _e( 'Information', LYF_DOMAIN ); ?>:</span></h3>
	<div class="inside">
	 	<p>
	 	<table border="0" cellpadding="10">
	 	<td>
    		<img src="<?php echo $pluginFolder;?>help.png"><a style="text-decoration:none;" href="http://www.wandererllc.com/company/plugins/listyofiles/" target="_blank"> <?php _e( 'Support and Help', LYF_DOMAIN ); ?> </a><br /><br />
			<a style="text-decoration:none;" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TC7MECF2DJHHY&lc=US" target="_blank"><img src="<?php echo $pluginFolder;?>paypal.gif"></a>
		</td>
		<td><a href="http://member.wishlistproducts.com/wlp.php?af=1080050" target="_blank"><img src="http://www.wishlistproducts.com/affiliatetools/images/WLM_120X60.gif" border="0"></a><br /></td>
    	<td><a href="http://www.woothemes.com/amember/go.php?r=39127&i=b18" target="_blank"><img src="http://woothemes.com/ads/120x90c.jpg" border=0 alt="WooThemes - WordPress themes for everyone" width=120 height=90></a></td>
    	</table>
    	<br />
    	<p><?php printf( __( 'Contact %s to sponsor a feature or write a plugin just for you.', LYF_DOMAIN ), '<a href="http://www.wandererllc.com/company/contact/" target="_blank">Wanderer LLC</a>', LYF_DOMAIN ); ?></p>
    	<p><?php printf( __( 'Leave a good rating or comments for %s.', LYF_DOMAIN ), '<a href="http://wordpress.org/extend/plugins/list-yo-files/" target="_blank">this plugin</a>', LYF_DOMAIN ); ?></p>
		</p>
		<iframe src="http://player.vimeo.com/video/18408849?byline=0&amp;portrait=0" width="532" height="299" frameborder="0" webkitAllowFullScreen allowFullScreen></iframe>
	</div>
</div>

<div class="postbox">
	<h3 class="hndle"><span>Displaying File Lists:</span></h3>
	<div class="inside">
		<p>To display a list of files in your posts, add the special <em>listyofiles</em> code
		to your page or post where you want to display the file list and include the folder
		to list.  For example:
		<small>[listyofiles folder="wp-content/gallery/my-new-gallery"]</small>.
		<strong>NOTE:</strong>  Do not add opening or closing slashes ("/") to the "folder" path.
		There are several options that you can chose as well to customize your file list:</p>

		<fieldset style="margin-left: 20px;">
		1. <em>sort</em> - include one of the following:  "alphabetic", "reverse_alphabetic", "filesize",
		"reverse_filesize", "date", or "reverse_date".  The <em>default</em> is "alphabetic" and
		is used if "sort" isn't specified.  Example usage:
		<small>[listyofiles folder="wp-content/gallery/my-new-gallery" sort="reverse_filesize"]</small><br /><br />

		2. <em>filter</em> - include a list of extensions (no period) separated by commas to only display
		matching files.  For example,
		<small>[listyofiles folder="wp-content/gallery/my-new-gallery" filter = "mp3,wav,aif"]</small> will
		only display audio files in your file list.  Not including this option will list all
		files in the specified folder.<br /><br />

		3. <em>options</em> - A list of comma-separated options to further customize your file list.  An example: <small>[listyofiles folder="wp-content/gallery/my-new-gallery" options="table,filesize,icon"]</small> Supported options:
		<fieldset style="margin-left: 20px;">
			<br>a. <em>table</em> - Renders your file list as a table (no border, though your CSS may override this).</br>
			<br>b. <em>filesize</em> - Includes the file size in the list.</br>
			<br>c. <em>date</em> - Includes the file modified date in the list.</br>
			<br>d. <em>new_window</em> - Will open links in a new window.</br>
			<br>e. <em>hide_extension</em> - Hides file extensions.</br>
			<br>f. <em>icon</em> - Works only with the <em>table</em> option.  This option displays a file
			icon to the left of the filename.  If you want to support
			additional file types, you can upload a 16x16 png file for the file type that you'd like to
			support.  The name of the file needs to match the extension that you want to display.  All letters should be lowercase.  For
			example, if you want to provide an icon for mp3 files, you would need to upload a file called
			"mp3.png" to the plugin's "icons" folder.</br>
			<br>g. <em>wpaudio</em> - <em>Requires the <a href="http://wordpress.org/extend/plugins/wpaudio-mp3-player/" target="_blank">WPAudio plugin</a></em>.  This option transforms the filename from a download link into an mp3 player.  <strong>Note:</strong> Use a filter to restrict files to mp3s.</br>
			<br>h. <em>wpaudiodownloadable</em> - <em>Requires the <a href="http://wordpress.org/extend/plugins/wpaudio-mp3-player/" target="_blank">WPAudio plugin</a></em>.  This option only works if <em>wpaudio</em> is also specified.  It adds a download link for the song.
			You can also use the "download" option as well for the same result.</br>
			<br>i. <em>audioplayer</em> - <em>Requires the <a href="http://wordpress.org/extend/plugins/audio-player/" target="_blank">Audio Player plugin</a></em>. This option transforms the filename from a download link into the AudioPlayer mp3 player.  <strong>Note:</strong> Use a filter to restrict files to mp3s. If you specify <em>table</em>, then each song will display in an individual player.  Otherwise, the songs will appear as a group in a single player.</br>
		</fieldset>
		<p />
		</fieldset>

		<p>There are some simplified codes as well:</p>

		<fieldset style="margin-left: 20px;">
			<p>To display a list of playable mp3s, add the special <em>showmp3s</em> code
			to the page or post where you want to display the files. For example:
			<small>[showmp3s folder='mruser/mymp3s']</small>. <em>Requires the WPAudio plugin</em></p>

			<p>To simplify showing files, use the <em>showfiles</em> code
			to the page or post where you want to display the files. For example:
			<small>[showfiles folder='mruser/somepdfs']</small>.</p>
		</fieldset>

		<p><strong>NOTE:</strong> These simplified codes are only available for user folders.  In other words, only subfolders
		underneath 'wp-content/list_yo_files_user_folders' will respond to these codes.</p>
	</div>
</div>

<div class="postbox">
	<h3 class="hndle"><span>Uploading Files:</span></h3>
	<div class="inside">
		<p>FTP is the usual method for uploading files to a specific folder on your website.  But, sometimes it can be inconvenient.
		List Yo' Files provides a simple <strong>Upload Files</strong> UI that allows you to avoid using FTP to upload
		your files without having to leave WordPress.  Follow these steps:</p>
		<fieldset style="margin-left: 20px;">
			1. In the Upload Files UI, indicate which folder in your WordPress installation you want to upload to (you can also type in a new folder and the folder will be created for you).  <em>Recommendation</em>:  Place the folder <strong>underneath the "wp-content" folder</strong>.  Make sure the folder is <a href="http://codex.wordpress.org/Changing_File_Permissions">readable and writable</a>.<br />
			<br>2. Generate a list of files by repeatedly browsing to each file you wish to upload to.</br><br />
			<br>3. Click on "Upload".</br>
		</fieldset>
		<p />
	</div>
</div>

<div class="postbox">
	<h3 class="hndle"><span>Deleting Files:</span></h3>
	<div class="inside">
		<p>FTP is also the usual method for deleting files in a specific folder on your website.
		List Yo' Files also provides a <strong>Delete Files</strong> UI that allows you to delete files
		without leaving WordPress.  Follow these steps:</p>
		<fieldset style="margin-left: 20px;">
			1. Type in the name of the folder you want to browse, again using your WordPress installation as the root folder.  For example:  "wp-content/gallery/my-new-gallery" will list the files in the "my-new-gallery" subfolder.<br />
			<br>2. Click the "List Files" button.</br><br />
			<br>3. Selectively click on "Delete" buttons to delete files.</br>
		</fieldset>
		<p />
	</div>
</div>

<div class="postbox">
	<h3 class="hndle"><span>About User Folders:</span></h3>
	<div class="inside">
		<p>If you have a site where users regularly log on and post material, you can use List Yo' Files to allow them to upload files and display
		them as lists.  This feature is enabled when you check the "Enable user folders" checkbox.</p>
		<p>When enabled, non-admin users will see the "Upload" and "Delete" menu items.  These pages are also simplified, requiring
		no typing other than naming folders.  Users are only allowed to upload files to subfolders under
		their user folder which is automatically created for them (underneath a special folder in 'wp-content').  They are also allowed basic file and folder management; they can create folders,
		upload files, delete files, and delete folders - again, only within their personal user folder.</p>
		<p>As admin, you have control over the following:</p>
		<fieldset style="margin-left: 20px;">
			1. Restricting the quantity of subfolders that the user can create.<br />
			<br>2. Enforcing a global size quota (in megabytes) per user.</br><br />
			<br>3. Selecting the minimum WordPress role that has access to the List Yo' Files software.</br><br />
			<br>4. Showing simplified help, which is intended to not overwhelm users with too many options.</br>
		</fieldset>
		<p />
	</div>
</div>

<?php
}
?>

</div>
</div>
</div>
</div>
