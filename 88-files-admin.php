<div class="wrap" style="max-width:950px !important;">
<h2><?php _e('Administer', LYF_DOMAIN); ?></h2>
<div id="poststuff" style="margin-top:10px;">
<div id="mainblock" style="width:710px">
<div class="dbx-content">

<form enctype="multipart/form-data" action="<?php echo $action_url ?>" method="POST">

<?php
wp_nonce_field('filez-nonce');
$pluginFolder = get_bloginfo('wpurl') . '/wp-content/plugins/' . dirname( plugin_basename( __FILE__ ) ) . '/';

include_once "information-box.php"
?>

<div id="listyofiles_admin" class="postbox" style="width:450px">
<h3 class='hndle'><span>List Yo' Files <?php _e( 'Administration:', LYF_DOMAIN ); ?></span></h3>
<div class="inside">

<p><?php _e( 'Rename the master menu to:', LYF_DOMAIN ); ?> <input type="text" name="menu_name" value="<?php echo $menuText;?>"size="25" /></p>

<p><input type=CHECKBOX name="on_use_table_styles" 
<?php
if ( "on" === $useTableStyles )
	print 'checked';
?>
> <?php _e('Use the lyf_table_styles.css located in the \'css\' folder to style file tables.  Feel free to edit this file.', LYF_DOMAIN ); ?></p>

<p><input type=CHECKBOX name="on_restrict_types"
<?php
if ( "on" === $restrictTypes )
	print 'checked';
?>
> <?php _e( 'Restrict uploads to the following file types (no periods, separated by commas):', LYF_DOMAIN ); ?> 

<input type="text" name="file_types" value="<?php echo $allowedFileTypes;?>"size="25" /> <small><?php _e( 'For example: "mp3,wav,aif,mov".', LYF_DOMAIN ); ?></small></p>

<?php
print '<p><input type=CHECKBOX name="on_enable_folders" ';
if ( "on" === $enableUserFolders )
	print 'checked';
print '> ';
_e( 'Enable user folders', LYF_DOMAIN ); 
print '</p>';
?>

<fieldset style="margin-left: 20px;">

<p><?php _e( 'Choose the minimum <a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank">role</a> that can manage files:', LYF_DOMAIN ); ?>  <select name="minimum_role">
<?php
	$roles = LYFGetRolesAndCapabilities();
	// Loop through each sub folder
	foreach( $roles as $role => $capability )
	{
		$selText = ( $minimumRole == $role ) ? '<option selected>' : '<option>';
		// print an option for each folder
		print $selText . $role . '</option>';
	}
?>
</select>
<br><small><?php _e( 'The least powerful role is "Subscriber", the most powerful is "Administrator".', LYF_DOMAIN ); ?></small></br>
</p>

<p><?php _e( 'Limit the number of user folders to:', LYF_DOMAIN ); ?> <input type="text" name="num_folders" value="<?php echo $subfolderCount;?>"size="10" />
<small><?php _e( 'Leave empty for unlimited', LYF_DOMAIN ); ?></small>
</p>

<p><?php _e( 'Upload space per user (in MB):', LYF_DOMAIN ); ?> <input type="text" name="folder_size" value="<?php echo $folderSize;?>"size="10" />
<small><?php _e( 'Leave empty for unlimited', LYF_DOMAIN ); ?></small>
</p>

<?php
print '<p><input type=CHECKBOX name="on_enable_simple_help" ';
if ( "on" === $enableSimpleHelp )
	print 'checked';
print '> ';
_e( 'Show simple help for users <small>(shows non-admins only the most basic options)', LYF_DOMAIN );
print '</small></p>';
?>

</fieldset>

<p><input type=CHECKBOX name="email_notifications" 
<?php
if ( "on" === $emailNotifications )
	print 'checked';
?>
> <?php _e('Enable notification by email when a file is uploaded. Enter the notification email addresses here (separated by commas):', LYF_DOMAIN ); ?></p>

<fieldset style="margin-left: 20px;">
	<input type="text" name="notification_emails" value="<?php echo $notificationEmails;?>"size="35" />
</fieldset>

<div><p><input type="submit" class="button-primary" name="save_admin_settings" value="<?php _e('Save Settings', LYF_DOMAIN ); ?>" /></p></div>

<div class="clear"></div>
</div>
</div>

</form>

</div>
</div>
</div>
</div>
