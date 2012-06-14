<div class="wrap" style="max-width:950px !important;">
<h2><?php _e('Delete Files', LYF_DOMAIN); ?></h2>
<div id="poststuff" style="margin-top:10px;">
<div id="mainblock" style="width:710px">
<div class="dbx-content">

<form action="<?php echo $action_url ?>" method="post">

<?php
wp_nonce_field( 'filez-nonce' );
$pluginFolder = get_bloginfo('wpurl') . '/wp-content/plugins/' . dirname( plugin_basename( __FILE__ ) ) . '/';

// Variable to see if subscriber folders is turned ON
$enableUserFolders = get_option( LYF_ENABLE_USER_FOLDERS );

include_once "information-box.php"
?>

<?php
if ( "on" == $enableUserFolders && !current_user_can( 'delete_users' ) )
{
	// Generate the list of user folders
	$userFolder = LYFGetUserUploadFolder( TRUE );
	$folders = LYFGenerateFolderList( $userFolder );

	// This is used to determine if the buttons should be disabled
	$folderCount = count( $folders );
?>

<div id="delete_folders" class="postbox" style="width:450px">
<h3 class='hndle'><span><?php _e( 'Delete Folders', LYF_DOMAIN ); ?></span></h3>
<div class="inside">

<p><?php _e( 'Select a folder you want to delete.  Then, click on the "Delete Folder" button.  <em>Be careful!</em> This will delete all files in the folder.', LYF_DOMAIN ); ?></p>

<p><?php _e( 'Select a folder to delete:', LYF_DOMAIN ); ?>  <select name="folder_to_delete">
<?php
	// Loop through each sub folder
	foreach( $folders as $folder )
	{
		// print an option for each folder
		print '<option>' . $folder . '</option>';
	}
?>
</select>
<input type="submit" <?php if ( 0 === $folderCount ) echo 'disabled="disabled" ';?>name="delete_folder" value="<?php _e( 'Delete Folder', LYF_DOMAIN ); ?>" /></div>
</p>

<div class="clear"></div>
</div>
</div>

<div id="delete_files" class="postbox" style="width:450px">
<h3 class='hndle'><span><?php _e( 'Delete Files', LYF_DOMAIN ); ?></span></h3>
<div class="inside">

<p><?php _e( 'Select a folder whose files you want to display.  Then, click on the "List Files" button to show each file.  Each file will then be listed and you can selectively delete your files.', LYF_DOMAIN ); ?></p>

<p><?php _e( 'Select a folder to display its files:', LYF_DOMAIN ); ?>  <select name="folder">
<?php
	// Loop through each sub folder
	foreach( $folders as $folder )
	{
		$selected = ( 0 === strcmp( $folder, $selectedListFolder ) ) ? ' selected>' : '>';

		// print an option for each folder
		print '<option' . $selected . $folder . '</option>';
	}
?>
</select>
</p>
<input type="submit" <?php if ( 0 === $folderCount ) echo 'disabled="disabled" ';?>name="list_user_files" value="<?php _e('List Files', LYF_DOMAIN ) ?>" />
</div>

<div class="clear"></div>
</div>
</div>

<?php
}
else
{
?>

<div id="delete_files" class="postbox" style="width:450px;height:250px">
<h3 class='hndle'><span><?php _e('Delete Files', LYF_DOMAIN ); ?></span></h3>
<div class="inside">

<p><?php _e('Using this "Delete Files" settings panel will conveniently allow you to avoid using FTP to delete your files.  Follow these steps:', LYF_DOMAIN ); ?></p>
<br><?php _e('1. Type in the name of the folder you want to browse, again using your WordPress installation as the root folder.  For example:  "wp-content/gallery/my-new-gallery" will list the files in the "my-new-gallery" subfolder.', LYF_DOMAIN ); ?></br>
<br><?php _e('2. Click the "List Files" button.', LYF_DOMAIN ); ?></br>
<br><?php _e('3. Selectively click on "Delete" buttons to delete files.', LYF_DOMAIN ); ?></br>
<p />
<p><?php _e( 'As always, <em>be careful</em> when deleting files!', LYF_DOMAIN ); ?></p>

<p><div><?php _e( 'Folder to list:', LYF_DOMAIN ); ?> <input type="text" name="folder" size="35" /><input type="submit" class="button-primary" name="list_files" value="<?php _e( 'List Files', LYF_DOMAIN ); ?>" /></div></p>

<div class="clear"></div>
</div>
</div>

<?php
}
// NOTE:	One "</form> and four "</div>" statements used to be here.  They have
// 			been moved back into the calling function in order to improve formatting.
?>