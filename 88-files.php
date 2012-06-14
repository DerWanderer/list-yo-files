<?php
/*
Plugin Name: List Yo' Files
Plugin URI: http://www.wandererllc.com/company/plugins/listyofiles/
Description: Lets WordPress users display lists of files in their pages and posts in a myriad of interesting ways.
Version: 1.12
Author: Wanderer LLC Dev Team
*/

require_once "helpers.php";
require_once "upload_ui.php";

// Important ID names
define( 'LYF_LIST_YO_FILES', 'List Yo\' Files' );
define( 'LYF_USER_FOLDER', 'wp-content/list_yo_files_user_folders/' );
define( 'LYF_DOMAIN', 'list-yo-files' );
define( 'LYF_ADMIN', 1 );
define( 'LYF_USER', 2 );
define( 'LYF_USER_MP3S', 3 );

// Database options
define( 'LYF_MENU_TEXT', 'lyf_menu_text' );
define( 'LYF_ENABLE_USER_FOLDERS', 'lyf_user_folders' );
define( 'LYF_ENABLE_SIMPLE_HELP', 'lyf_simple_help' );
define( 'LYF_MINIMUM_ROLE', 'lyf_minimum_role' );
define( 'LYF_ENABLE_ALLOWED_FILE_TYPES', 'lyf_enable_allowed_file_types' );
define( 'LYF_USE_TABLE_STYLES', 'lyf_use_table_styles');
define( 'LYF_ALLOWED_FILE_TYPES', 'lyf_allowed_file_types' );
define( 'LYF_USER_SUBFOLDER_LIMIT', 'lyf_subfolder_limit' );
define( 'LYF_USER_USER_FOLDER_SIZE', 'lyf_user_folder_size' );
define( 'LYF_ENABLE_EMAIL_NOTIFICATIONS', 'lyf_email_notifications' );
define( 'LYF_NOTIFICATION_EMAILS', 'lyf_notification_emails' );

// Other strings and messages
define( 'ADMINISTRATOR', 'Administrator' );

// Localized
define( 'EMPTY_FOLDER', 'No files found.' );
define( 'SHORTCODE_ERROR', 'Incorrect shortcode.  Check what you typed between the "[]".' ) ;
define( 'PERMISSIONS_MESAGE', 'You do not have sufficient permissions to access this page. Resave your administration options to be safe.' );

// Various hooks and actions for this plug-in
add_shortcode( 'listyofiles', 'LYFShowAdminFiles' );
add_shortcode( 'showfiles', 'LYFShowUserFiles' );
add_shortcode( 'showmp3s', 'LYFShowMP3Files' );
add_shortcode( 'listyofiles_uploadform', 'LYFUploadForm' );

add_action( 'admin_menu', 'LYFAddSettingsPage' );
add_action( 'init', 'LoadDomain' );									// Localization
add_action( 'wp_print_styles', 'LYF_AddStyles' );					// Needed for adding a styles
add_filter( 'plugin_row_meta', 'AddListYoFilesPluginLinks', 10, 2 );// Expand the links on the plugins page

// Add extra links to the plugin summary on the WordPress plugins menu
function AddListYoFilesPluginLinks($links, $file)
{
	if ( $file == plugin_basename(__FILE__) )
	{
		$links[] = '<a href="http://wordpress.org/extend/plugins/list-yo-files/">' . __('Overview', LYF_DOMAIN ) . '</a>';
		$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TC7MECF2DJHHY&lc=US">' . __('Donate', LYF_DOMAIN ) . '</a>';
	}
	return $links;
}

//	LYF_AddStyles will add custom styles for List Yo' Files.
function LYF_AddStyles()
{
	$url = WP_PLUGIN_URL . '/list-yo-files/css/lyf_table_styles.css';
	$styleFile = WP_PLUGIN_DIR . '/list-yo-files/css/lyf_table_styles.css';
	if ( file_exists( $styleFile ) && 'on' === get_option( LYF_USE_TABLE_STYLES ) )
	{
		wp_register_style('LYFStyleSheets', $url);
		wp_enqueue_style( 'LYFStyleSheets');
	}
}

// Localization support
function LoadDomain()
{
	// Load the translation
	$path = basename( dirname( __FILE__ ) ) . '/lang';
	// Docs say this function doesn't return a value, but it actually returns a bool.
	// So, examine that variable if things get weird.
	$result = load_plugin_textdomain( LYF_DOMAIN, false, $path );
}

// Global counter for distinguishing multiple lists
$fileListCounter = 1;

//
//	LYFShowAdminFiles
//
function LYFShowAdminFiles( $params )
{
	return LYFDisplayFiles( $params, LYF_ADMIN );
}

//
//	LYFShowUserFiles
//
function LYFShowUserFiles( $params )
{
	return LYFDisplayFiles( $params, LYF_USER );
}

//
//	LYFShowMP3Files
//
function LYFShowMP3Files( $params )
{
	return LYFDisplayFiles( $params, LYF_USER_MP3S );
}

//
//	LYFUploadForm
//
function LYFUploadForm( $params )
{
	// Store the various options values in an array.
	$values = shortcode_atts( array( 'options' => '' ), $params );
	$options = $values['options'];
	$output =  LYFStartForm();
	$output .= LYFGetUploadFormCode( $options );
	$ouptut .= LYFEndForm();
	return $output;
}

//
//	LYFDisplayFiles()
//
// 	This function reads the shortcode from the blog post or page and displays the
//	list of files for the folder requested.  Several options are allowed, see these
// 	in the $values variable.  This function ultimately generates an HTML table to
// 	display the list of files.
//
function LYFDisplayFiles( $params, $mode )
{
	// Store the various options values in an array.
	$values = shortcode_atts( array( 	'folder' => '',
									 	'link' => '',
										'sort' => '',
										'filter' => '',
										'wpaudio' => '',
										'options' => ''
									), $params );

	// Get the folder and link options.
	// Here's a difference in modes...simply generates a different folder to
	// simplify the shortcode for the user.
	if ( LYF_ADMIN === $mode )
	{
		// Read the folder as if it's constructed off the site's route.
		$folder = $values['folder'];
	}
	elseif ( LYF_USER === $mode )
	{
	    // First, get the full path to user's upload folder.  Create it if it doesn't exist yet.
		$folder = LYFGetUserUploadFolder( TRUE );

		// If the folder doesn't exist and user folders has been enabled in the
		// admin panel, then create the user folder.
		if ( !is_dir( $folder ) && 'on' === get_option( LYF_ENABLE_USER_FOLDERS ) )
		{
			$result = LYFCreateUserFolder( $folder );
		}

		// No "folder" argument needed for this one, just get the user's folder.
		// Relative path this time.
		$folder = LYFGetUserUploadFolder( FALSE );
	}
	elseif ( LYF_USER_MP3S === $mode )
	{
		// Allow the user to pass only the name of the subfolder they want to list.
//		$folder = LYF_USER_FOLDER . $values['folder'];
		$folder = LYFGetUserUploadFolder( FALSE ) . $values['folder'];
	}
	else
	{
		return '<p><em>'. __( SHORTCODE_ERROR, LYF_DOMAIN ) .'</em></p>';
	}

	$link = $values['link'];
	$sort = $values['sort'];
	// Special mode for mp3s
	if ( LYF_USER_MP3S === $mode )
	{
		$options = $values['options'];
		$options .= ',table,wpaudio';
		$filter = $values['filter'];
		$filter .= ',mp3';
	}
	else
	{
		$filter = $values['filter'];
		$options = $values['options'];
	}

	// Warn the user if there is no "folder" argument
	if ( empty( $folder ) )
	{
		return '<p><em>' . __('Warning: There is no "folder" shortcode specified.', LYF_DOMAIN ) . '</em></p>';
	}

	// "link" isn't currently exposed, so this is most likely just blank.  So, set
	// it to $folder.
	if ( '' == $link )
	{
		$link = $folder;
	}

	// The $filelist variable will hold a list of files.
	$filelist = LYFGenerateFileList( $folder, $link, $filter );

	// if there are no items, this folder is empty.
	if( !count( $filelist ) )
	{
		// Show the user that there are no files.
		return '<p><em>'. __( EMPTY_FOLDER, LYF_DOMAIN ) .'</em></p>';
	}
	else
	{
		// Using the list of files, generate an HTML representation of the folder.
		$output = LYFListFiles( $filelist, $sort, $options );
		return $output;
	}
}

//
// LYFGenerateFileList()
//
// @param $path - the folder to list, relative the the WordPress installation.
// @param $linkTarget - currently unused and requested to be the exact same
//	value as $path.  With this relative path info, the function will loop
//	through each file matching the criteria and add resulting files to a list
//	which are returned.
// @param $filter - Pass a filter ('*.txt', for example) to apply to the list
//	of files to display.
//
function LYFGenerateFileList( $path, $linkTarget, $filter )
{
	// array to build the list in
	$filelist = array();

	// Convert to the absolute path
	$path = ABSPATH . $path;

	// Attempt to open the folder
	if ( ( $p = @opendir( $path ) ) !== FALSE )
	{
		// Read the directory for items inside it.
		while ( ( $item = readDir( $p ) ) !== false )
		{
			// Find the file's extension then determine if a filter is turned
			// on and this file type fits the filter.
			$ext = substr( strrchr( $item, '.' ), 1 );
			$canView = true;
			if ( !empty( $filter ) )
			{
				if ( FALSE === stripos( $filter, $ext ) )
					$canView = false;
			}

			// Exclude dotfiles, current, and parent dirs.  Also skip if the
			// filter doesn't yield a match.
			if ( $item[0] != '.' && $canView )
			{
				// Set up the relative path to the item.
				// START:
				// Code suggested by Peter Liu on 7/1/2011 for encoding UTF-8 files.
				// I changed it slightly to use rawurlencode() instead of urlencode()
				// since this code fetches from the filesystem, not a URL.  See
				// php.net/rawurlencode for more on spaces and "+" symbols.
				$newPath = $path . '/' . $item;
				$temparr = explode( '/' , $linkTarget );

				// Have to assemble the path this way because we can't encode the "/" character!
				$assembledPath = "";
				for( $i = 0; $i<count($temparr); ++$i )
				{
					// Use rawurlencode() to properly encode spaces for the file system.
				   $assembledPath .= rawurlencode( $temparr[$i] ) . '/';
				}
				$newTarget = $assembledPath . rawurlencode( $item );
				// END:  Code suggested by Peter Liu.

				// If current item is a file, do more stuff.  Otherwise, just skip it.
				if ( is_file( $newPath ) )
				{
					// Special processing for links.  Read the path to the link and store it.
					if ( function_exists( 'is_link' ) && is_link( $newPath ) )
						$filelist[$item]['slTarget'] = readlink( $newPath );

					// Save the paths.
					$filelist[$item]['path'] = $newPath;
					$filelist[$item]['link'] = $newTarget;
					$filelist[$item]['size'] = filesize( $newPath );
					$filelist[$item]['date'] = filemtime( $newPath );
				}
			}
		}
		closeDir($p);
	}
	return $filelist;
}

//
// LYFListFiles()
//
// This function takes a list of files and generates an HTML table to show them inside.
//
function LYFListFiles( $filelist, $sort, $options )
{
	// Use this as a static variable
	global $fileListCounter;

	// Sort the items
	if ( 'reverse_alphabetic' == $sort )
	{
		// Reverse alphabetically sort
		krsort( $filelist );
	}
	elseif ( 'reverse_filesize' == $sort )
	{
		uasort( $filelist, 'ReverseFileSizeSort' );
	}
	elseif ( 'filesize' == $sort )
	{
		uasort( $filelist, 'FileSizeSort' );
	}
	elseif ( 'reverse_date' == $sort )
	{
		uasort( $filelist, 'ReverseDateSort' );
	}
	elseif ( 'date' == $sort )
	{
		uasort( $filelist, 'DateSort' );
	}
	else
	{
		// By default, alphabetically sort
		ksort( $filelist );
	}

	// Convert options into booleans

	$files = '';

	// Get the URL to the blog.  The path to the files will be added to this.
	$wpurl = get_bloginfo( "wpurl" );

	// Get the various options
	$isTable = ( FALSE !== stripos( $options, 'table' ) );
	$isNewWindow = ( FALSE !== stripos( $options, 'new_window' ) );
	$isHideExtension = ( FALSE !== stripos( $options, 'hide_extension' ) );
	$isFilesize = ( FALSE !== stripos( $options, 'filesize' ) );
	$isDate = ( FALSE !== stripos( $options, 'date' ) );
	$isIcon = ( FALSE !== stripos( $options, 'icon' ) );
	$isWPAudio = ( FALSE !== stripos( $options, 'wpaudio' ) );
	$isWPAudioDownloadable = ( FALSE !== stripos( $options, 'wpaudiodownloadable' ) || FALSE !== stripos( $options, 'download' ) );
	$isAudioPlayer = ( FALSE !== stripos( $options, 'audioplayer' ) );

	// Start generating the HTML
	$retVal = "<div id='filelist$fileListCounter'>";

	// Generate either a table or a list based on the user's options
	if ( $isTable )
	{
		$retVal .= '<table width="100%" border="0" cellpadding="7">>'.PHP_EOL;
		foreach( $filelist as $itemName => $item )
		{
			$retVal .= '<tr>'; // <- missing '<tr>' reported by user (4/10/12)
			// Get file variables
			$size = LYFFormatFileSize( $item['size'] );
			//$date = date( "F j, Y", $item['date'] );
			$date = date( "n/j/Y g:i a", $item['date'] ); // <-suggested by user ("ListYoFiles v 1.02" Jan 2011 email)
			$link = $wpurl.'/'.$item['link'];

			// Generate list elements

			// Generate a column for icons
			if ( $isIcon )
			{
				$ext = substr( strrchr( $item['link'], '.' ), 1 );
				$ext = strtolower( $ext );
				$pluginFolder = $wpurl . '/wp-content/plugins/' . dirname( plugin_basename( __FILE__ ) ) . '/';
				if ( file_exists( dirname ( __FILE__ ) . '/' . "icons/$ext.png" ) )
				{
					$extensionFile = $pluginFolder . "icons/$ext.png";
				}
				else
				{
					$extensionFile = $pluginFolder . "icons/generic.png";
				}
				$retVal .= '<td class="lyf_td_icons" width="16"><img src="'.$extensionFile.'"></td>'.PHP_EOL;
			}

			// Strip extension if necessary
			if ( $isHideExtension )
			{
				$ext = substr( strrchr( $itemName, '.' ), 0 );
				$itemName = str_replace( $ext, '', $itemName );
			}

			// This part is required.  However, it can be altered by the "wpaudio" option
			if ( TRUE === $isWPAudio )
			{
				$onOff = ($isWPAudioDownloadable) ? $link : "0";
				$wpaudioProcessed = do_shortcode( '[' . "wpaudio url=\"$link\" text=\" $itemName\" dl=\"$onOff\"" . ']' );
				$retVal .= '<td class="lyf_td_audio">' . $wpaudioProcessed . '</td>' . PHP_EOL;
			}
			elseif ( TRUE === $isAudioPlayer )
			{
				if ( class_exists('AudioPlayer') )
				{
					// Hey, thanks for insert_audio_player()!  I just don't want
					// it to echo the results.
					global $AudioPlayer;
					$apProcessed =  $AudioPlayer->processContent( '[audio:' . $link . ']' );
					$retVal .= '<td class="lyf_td_audio>' . $apProcessed . '</td>';
				}
			}
			else // This is the primary element - the linked file.
			{
				// Show links in a new window or not?
				if ( $isNewWindow )
					$retVal .= '<td class="lyf_td_filename"><a href="'.$link.'" target="_blank">'.utf8_encode($itemName).'</a></td>'.PHP_EOL;
				else
					$retVal .= '<td class="lyf_td_filename"><a href="'.$link.'">'.utf8_encode($itemName).'</a></td>'.PHP_EOL;
			}

			// Show the file size
			if ( $isFilesize )
				$retVal .= '<td class="lyf_td_size">'.$size.'</td>'.PHP_EOL;

			// Show the date
			if ( $isDate )
				$retVal .= '<td class="lyf_td_date">'.$date.'</td>'.PHP_EOL;
			$retVal .= '</tr>';
		}
		$retVal .= '</table>'.PHP_EOL;
	}
	elseif ( TRUE === $isAudioPlayer )
	{
		if ( class_exists('AudioPlayer') )
		{
			global $AudioPlayer;
			// First, just generate a list of comma-separated links
			$links = "";
			foreach( $filelist as $itemName => $item )
			{
				$link = $wpurl.'/'.$item['link'];
				$links .= "$link,";
			}
			$links = rtrim( $links, ',' );
			$apProcessed =  $AudioPlayer->processContent( '[audio:' . $links . ']' );
			$retVal .= '<td>' . $apProcessed . '</td>';
		}
	}
	else
	{
		foreach( $filelist as $itemName => $item )
		{
			// Get file variables
			$size = LYFFormatFileSize( $item['size'] );
			//$date = date( "F j, Y", $item['date'] );
			$date = date( "n/j/Y g:i a", $item['date'] );
			$link = $wpurl.'/'.$item['link'];

			// Strip extension if necessary
			if ( $isHideExtension )
			{
				$ext = substr( strrchr( $itemName, '.' ), 0 );
				$itemName = str_replace( $ext, '', $itemName );
			}

			if ( $isNewWindow )
				$files .= '<li><a href="'.$link.'" target="_blank">'.utf8_encode( $itemName ).'</a>';
			else
				$files .= '<li><a href="'.$link.'">'.utf8_encode( $itemName ).'</a>';

			if ( $isFilesize )
				$files .= '<span class="size">' . __('Size: ', LYF_DOMAIN ) . $size . '</span>' . PHP_EOL;

			if ( $isDate )
				$files .= '<span class="modified">' . __('Date: ', LYF_DOMAIN ) . $date . '</span>' . PHP_EOL;

			$files .='</li>'.PHP_EOL;
		}

		// Encase the ouput in class and ID
		$fileListCounter++;
		$retVal .= '<ul id="listyofiles">'.PHP_EOL.$files.'</ul>'.PHP_EOL;
	}

	// Close out the div
	$retVal .= '</div>'.PHP_EOL;

	// return the HTML
	return $retVal;
}

//
// LYFAddSettingsPage()
//
// This function is called by WordPress to add settings menus to the Dashboard.  It adds two menus:
// one for uploading files and one for deleting files.
//
function LYFAddSettingsPage()
{
	// The master menu text is dynamic.
	$menuText = get_option( LYF_MENU_TEXT );
	if ( 0 == strlen( $menuText ) )
		$menuText = LYF_LIST_YO_FILES;

	// Set the page title
	$pageText = $menuText . ' Options';

	// Get the minimum role for uploading and deleting
	$minimumRole = get_option( LYF_MINIMUM_ROLE );
	if ( 0 == strlen( $minimumRole ) )
		$minimumRole = ADMINISTRATOR;

	// Get the master list of roles and capabilities for comparing;
	$roles = LYFGetRolesAndCapabilities();

	$pluginFolder = get_bloginfo('wpurl') . '/wp-content/plugins/' . dirname( plugin_basename( __FILE__ ) ) . '/';
	add_menu_page( $pageText, $menuText, $roles[$minimumRole], basename(__FILE__), 'LYFHandleAboutPage', $pluginFolder . 'dn-up-2.png' );
    add_submenu_page( basename(__FILE__), __('Usage', LYF_DOMAIN ), __('Usage', LYF_DOMAIN ), $roles[$minimumRole], basename(__FILE__), 'LYFHandleAboutPage' );
	add_submenu_page( basename(__FILE__), __('Upload Files', LYF_DOMAIN ), __('Upload Files', LYF_DOMAIN ), $roles[$minimumRole], 'Upload', 'LYFHandleUploadFilesPage' );
    add_submenu_page( basename(__FILE__), __('Delete Files', LYF_DOMAIN ), __('Delete Files', LYF_DOMAIN ), $roles[$minimumRole], 'Delete', 'LYFHandleDeleteFilesPage' );
    add_submenu_page( basename(__FILE__), __('Administer', LYF_DOMAIN ) . ' List Yo\' Files', __('Administer', LYF_DOMAIN ), $roles[ADMINISTRATOR] , 'Administer', 'LYFHandleAdminPage' );
}

//
// LYFHandleAdminPage()
//
// This function handles the all-important admin page.
//
function LYFHandleAdminPage()
{
	$menuText = get_option( LYF_MENU_TEXT );
	$useTableStyles = get_option( LYF_USE_TABLE_STYLES );
	$restrictTypes = get_option( LYF_ENABLE_ALLOWED_FILE_TYPES );
	$allowedFileTypes = get_option( LYF_ALLOWED_FILE_TYPES );
	$enableUserFolders = get_option( LYF_ENABLE_USER_FOLDERS );
	$enableSimpleHelp = get_option( LYF_ENABLE_SIMPLE_HELP );
	$minimumRole = get_option( LYF_MINIMUM_ROLE );
	if ( 0 == strlen( $minimumRole ) )
		$minimumRole = ADMINISTRATOR;
	$subfolderCount = get_option( LYF_USER_SUBFOLDER_LIMIT );
	$folderSize = get_option( LYF_USER_USER_FOLDER_SIZE );
	$notificationEmails = get_option( LYF_NOTIFICATION_EMAILS );
	$emailNotifications = get_option( LYF_ENABLE_EMAIL_NOTIFICATIONS );

	// The user must be an admin to see this page, no matter what is selected
	// in the admin page.
	$roles = LYFGetRolesAndCapabilities();
	if ( !current_user_can( $roles[ADMINISTRATOR] ) )
	{
    	wp_die( __( PERMISSIONS_MESAGE, LYF_DOMAIN ) );
  	}

	if ( isset( $_POST['save_admin_settings'] ) )
	{
		// Security check
		check_admin_referer( 'filez-nonce' );

		// Save the menu text
		$menuText = $_POST['menu_name'];
		update_option( LYF_MENU_TEXT, $menuText );

		// CSS
		$useTableStyles = $_POST['on_use_table_styles'];
		update_option( LYF_USE_TABLE_STYLES, $useTableStyles );

		// File types
		$restrictTypes = $_POST['on_restrict_types'];
		update_option( LYF_ENABLE_ALLOWED_FILE_TYPES, $restrictTypes );

		$allowedFileTypes = $_POST['file_types'];
		update_option( LYF_ALLOWED_FILE_TYPES, $allowedFileTypes );

		// Save various user folder options
		$enableUserFolders = $_POST['on_enable_folders'];
		update_option( LYF_ENABLE_USER_FOLDERS, $enableUserFolders );

		if ( "on" === $enableUserFolders )
		{
			$enableSimpleHelp = $_POST['on_enable_simple_help'];
			update_option( LYF_ENABLE_SIMPLE_HELP, $enableSimpleHelp );

			$minimumRole = $_POST['minimum_role'];
			update_option( LYF_MINIMUM_ROLE, $minimumRole );

			$subfolderCount = $_POST['num_folders'];
			update_option( LYF_USER_SUBFOLDER_LIMIT, $subfolderCount );

			$folderSize = $_POST['folder_size'];
			update_option( LYF_USER_USER_FOLDER_SIZE, $folderSize );
		}

		// Email notifications
		$emailNotifications = $_POST['email_notifications'];
		update_option( LYF_ENABLE_EMAIL_NOTIFICATIONS, $emailNotifications );

		$notificationEmails = $_POST['notification_emails'];
		update_option( LYF_NOTIFICATION_EMAILS, $notificationEmails );

		echo '<div id="message" class="updated fade">' . __('Successfully saved your settings.', LYF_DOMAIN ) . '</div>';
	}

	// Include the settings page here.
	include('88-files-admin.php');
}

//
// LYFHandleAboutPage()
//
// This function handles the very simple "about" page.
//
function LYFHandleAboutPage()
{
	// Get variables for checking access
	$minimumRole = get_option( LYF_MINIMUM_ROLE );
	if ( 0 == strlen( $minimumRole ) )
		$minimumRole = ADMINISTRATOR;
	$roles = LYFGetRolesAndCapabilities();

	// Stop the user if they don't have permission
	if ( !current_user_can( $roles[$minimumRole] ) )
	{
    	wp_die( __( PERMISSIONS_MESAGE, LYF_DOMAIN ) );
  	}

	// Include the settings page here.
	include('88-files-about.php');
}

//
// LYFHandleUploadFilesPage()
//
// This function handles the page that manages uploading files and occasionally
// creating folders.
//
function LYFHandleUploadFilesPage()
{
	// Get variables for checking access
	$minimumRole = get_option( LYF_MINIMUM_ROLE );
	if ( 0 == strlen( $minimumRole ) )
		$minimumRole = ADMINISTRATOR;
	$roles = LYFGetRolesAndCapabilities();

	// Stop the user if they don't have permission
	if ( !current_user_can( $roles[$minimumRole] ) )
	{
    	wp_die( __( PERMISSIONS_MESAGE, LYF_DOMAIN ) );
  	}

  	// If the upload_files POST option is set, then files are being uploaded
	if ( isset( $_POST['upload_files'] ) )
	{
		// Security check
		check_admin_referer( 'filez-nonce' );

		$uploadFolder = ABSPATH . $_POST['upload_folder'];
		$output = LYFUploadFiles( $uploadFolder );
		echo $output;
	}

	// This is the handler for the users other than admins
	if ( isset($_POST['upload_user_files'] ) )
	{
		// Security check
		check_admin_referer( 'filez-nonce' );

		// Little hack
		$canUpload = TRUE;

		// Save the selected folder
		$selectedUploadFolder = $_POST['upload_folder'];
		// Get the users's base upload folder
		$uploadFolder = LYFGetUserUploadFolder( TRUE );

		// Check that the user is not trying to upload a file when there's no user
		// folder.
		if ( 0 === strlen( $selectedUploadFolder ) )
		{
			echo '<div id="message" class="updated fade">' . __('<strong>Failed</strong> to upload. You need to create and choose a subfolder to upload to.', LYF_DOMAIN ) . '</div>';
			$canUpload = FALSE;
		}

		// Any folder size restrictions?
		$maxFolderSize = get_option( LYF_USER_USER_FOLDER_SIZE );
		if ( 0 !== strlen( $maxFolderSize ) && $canUpload )
		{
			$filesSize = LYFGetFolderSize( $uploadFolder );
			$sizeInKB = $maxFolderSize * 1024 * 1024;

			if ( $sizeInKB < $filesSize )
			{
				echo '<div id="message" class="updated fade">' . __('<strong>Failed</strong> to upload. You have already uploaded your size quota.', LYF_DOMAIN ) . '</div>';
				$canUpload = FALSE;
			}
		}

		if ( $canUpload )
		{
			// Now, tack on the folder they want to upload to.
			$uploadFolder .= $selectedUploadFolder;
			$output = LYFUploadFiles( $uploadFolder );
			echo $output;
		}
	}

	// If a folder is being created
	if ( isset( $_POST['create_folder'] ) )
	{
		check_admin_referer( 'filez-nonce' );

		// Get these variables.  Needed to determine if there are restrictions
		// on the number of subfolders
		$userFoldersEnabled = get_option( LYF_ENABLE_USER_FOLDERS );
		$folderLimit = get_option( LYF_USER_SUBFOLDER_LIMIT );

		// Little hack
		$allowCreate = TRUE;

		// Get the user's folder
		$createFolder = LYFGetUserUploadFolder( TRUE );

		// Is the folder count restriction turned on and set?
		if ( "on" === $userFoldersEnabled && !empty( $folderLimit ) )
		{
			// Count the number of subfolders
			$userSubFolders = LYFGenerateFolderList( $createFolder );
			$folderCount = count( $userSubFolders );

			// If the folder count is equal or greater, don't allow any
			// more folders to be created.
			if ( $folderCount >= $folderLimit )
			{
				$message = '<div id="message" class="updated fade">' . __('<strong>Failed</strong> to create the subfolder.  You have already reached your subfolder limit.', LYF_DOMAIN ) . '</div>';
				$allowCreate = FALSE;
			}
		}

		if ( $allowCreate )
		{
			$createFolder .= $_POST['folder'];
			// Check here to see if the user is trying to trick the system into
			// creating folders in different locations.  Basically, check for a
			// few illegal characters.
			$result = LYFIsValidFolderName( $_POST['folder'] );
			if ( $result > 0 )
			{
				// If the folder name is legit, then try to create the folder.
				$result = LYFCreateUserFolder( $createFolder );
			}
			$message = '<div id="message" class="updated fade">';
			$message .= LYFConvertError( $result, $_POST['folder'] );
			$message .= '</div>';
		}
		echo $message;
	}

	// The file that will handle uploads is this one (see the "if"s above)
	$action_url = $_SERVER['REQUEST_URI'];

	// Include the settings page here.
	include('88-files-upload.php');
}

//
// LYFHandleDeleteFilesPage()
//
// This functions handles the delete files page.  It manages both displaying the page and deleting the
// files.
//
function LYFHandleDeleteFilesPage()
{
	// Get variables for checking access
	$minimumRole = get_option( LYF_MINIMUM_ROLE );
	if ( 0 == strlen( $minimumRole ) )
		$minimumRole = ADMINISTRATOR;
	$roles = LYFGetRolesAndCapabilities();

	// Stop the user if they don't have permission
	if ( !current_user_can( $roles[$minimumRole] ) )
	{
    	wp_die( __( PERMISSIONS_MESAGE, LYF_DOMAIN ) );
  	}

  	// This file will handle the deleting when "Delete" is pressed.
	$action_url = $_SERVER['REQUEST_URI'];

	// If the "list_files" POST option is set, then the user has requested to see the files in a folder.
	if ( isset( $_POST['list_files'] ) )
	{
		// Security check
		check_admin_referer( 'filez-nonce' );

		// Here's the source file which displays the page.  This is shown first because delete options are
		// shown at the bottom.
		include( '88-files-delete.php' );

		// This function will generate an array of any file in the folder to be deleted.
		$filelist = LYFGenerateFileList( $_POST['folder'], $_POST['folder'], '' );

		// if there are no items, this folder is empty.
		if( !count( $filelist ) )
		{
			// Show the user an empty folder message.
			echo '<p><em>' . __( EMPTY_FOLDER, LYF_DOMAIN ) . '</em></p>';
		}
		else
		{
			// List files to be deleted.
			echo LYFListFilesToDelete( $filelist, $_POST['folder'] );
		}
	}
	// This if block handles non-admin deletes
	else if ( isset( $_POST['list_user_files'] ) )
	{
		// Security check
		check_admin_referer( 'filez-nonce' );

		// Save this folder (also used in the delete admin page)
		$selectedListFolder = $_POST['folder'];

		// Here's the source file which displays the page.  This is shown first because delete options are
		// shown at the bottom.
		include( '88-files-delete.php' );

		// Generate the folder to list
		$listFolder = LYFGetUserUploadFolder( FALSE );
		$listFolder .= $selectedListFolder;

		// This function will generate an array of any file in the folder to be deleted.
		$filelist = LYFGenerateFileList( $listFolder, $listFolder, '' );

		// if there are no items, this folder is empty.
		if( !count( $filelist ) )
		{
			// Show the user an empty folder message.
			echo '<p><em>' . __( EMPTY_FOLDER, LYF_DOMAIN ) . '</em></p>';
		}
		else
		{
			// List files to be deleted.
			echo LYFListFilesToDelete( $filelist, $listFolder );
		}
	}
	// This if block let's non-admins delete their folders
	else if ( isset( $_POST['delete_folder'] ) )
	{
		// Security check
		check_admin_referer( 'filez-nonce' );

		$selectedListFolder = $_POST['folder_to_delete'];

		// Generate the folder to list
		$deleteFolder = LYFGetUserUploadFolder( TRUE );
		$deleteFolder .= $selectedListFolder;

		$deleteResult = LYFRemoveDirectory( $deleteFolder );

		if ( !$deleteResult )
		{
			$failedMessage = sprintf( __( "<strong>Failed</strong> to delete the folder %s", LYF_DOMAIN ), $selectedListFolder );
			echo '<div id="message" class="updated fade"><p>' . $failedMessage . '</p></div>';
		}
		else
		{
			$deletedMessage = sprintf( __( "The folder %s has been deleted.", LYF_DOMAIN ), $selectedListFolder );
			echo '<div id="message" class="updated fade"><p>' . $deletedMessage . '</p></div>';
		}

		// Here's the source file which displays the page.  This is shown first because delete options are
		// shown at the bottom.
		include( '88-files-delete.php' );
	}

	// If a GET value was passed, then the user wants to delete a file.
	else if ( isset( $_GET['id'] ) )
	{
		// Security check
		check_admin_referer( 'filez-nonce' );

		// Here's the source file which displays the page.  This is shown first because delete options are
		// shown at the bottom.
		include( '88-files-delete.php' );

		// Both the file and folder were passed, so save these off.
		$file = $_GET['id'];
		// Strip out C-style escaping to allow for filenames with words like "You've"
		$file =stripcslashes( $file );
		$folder = $_GET['folder'];

		// This is the PHP DeleteFile() function...nice name, huh?
		$result = unlink( ABSPATH . $folder . '/' . $file );

		// The "updated fade" class is that cool faded text area.
		if ( $result )
			echo '<div id="message" class="updated fade"><p>' . $file . ' has been deleted.</p></div>';
		else
			echo '<div id="message" class="updated fade"><p>' . $file . ' could not be deleted.</p></div>';

		// Regenerate the list of files now that one of them has been deleted.
		$filelist = LYFGenerateFileList( $folder, $folder, "" );

		// if there are no items, this folder is empty.
		if( !count( $filelist ) )
		{
			// Show the empty message.
			echo '<p><em>' . __( EMPTY_FOLDER, LYF_DOMAIN ) . '</em></p>';
		}
		else
		{
			// Show the files to delete again.
			echo LYFListFilesToDelete( $filelist, $folder );
		}
	}
	else	// This gets displayed when someone just visits the page.
	{
		// Here's the source file which displays the page.  This is shown first because delete options are
		// shown at the bottom.
		include( '88-files-delete.php' );
	}
?>
</form>
</div>
</div>
</div>
</div>
<?php
}

?>