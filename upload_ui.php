<?php

function LYFStartForm()
{
	$output = '<form enctype="multipart/form-data" action="' . $action_url . '" method="post">';
	return $output;
}

function LYFEndForm()
{
	$output = '</form>';
	return $output;
}

// This form creates the "create subfolder" UI
function LYFGetCreateSubfolderFormCode()
{
	$subfolderCount = ""; // FILL THIS IN

//	echo '<p>You must create at least one subfolder to upload your files to.  You may create ' . $subfolderCount . ' folders.</p>
//	<p>Folder name: <input type="text" name="folder" size="35" /></p><div class="submit"><input type="submit" name="create_folder" value="Create Folder" /></div>';
	return $output;
}

function LYFGetSelectSubfolderFormCode()
{
	// <p>Select a folder to upload files to:  <select name="upload_folder">

	$uploadFolder = LYFGetUserUploadFolder( TRUE );
	//$folders = LYFGenerateFolderList( $uploadFolder );

	// This is used to determine if the submit button should be disabled
	$folderCount = count( $folders );

	// Loop through each sub folder
	foreach( $folders as $folder )
	{
		// Automatically select the folder that the user may have previously
		// uploaded to.
		$selected = ( 0 === strcmp( $folder, $selectedUploadFolder ) ) ? ' selected>' : '>';

		// print an option for each folder
		print '<option' . $selected . $folder . '</option>';
	}

	echo '</select></p>';
	return $output;
}

// This file creates the upload UI
function LYFGetUploadFormCode( $options )
{
	// Variable to return
	$output = '';

	//
	// See if the user is logged in.  If not, get out.
	//

	$user = wp_get_current_user();
	if ( 0 === $user->ID )
	{
		$message = __('You must first log in before uploading files.');
		$output = '<p>'.$message.'</p>';
		return $output;
    }

	// This is the handler for the users other than admins
	if ( isset($_POST['upload_user_files'] ) )
	{
		// Security check
		check_admin_referer( 'filez-nonce' );

		// Little hack
		$canUpload = TRUE;

		// Get the users's base upload folder
		$uploadFolder = LYFGetUserUploadFolder( TRUE );

		//
		// Next, see if "User Folders" is enabled
		//

		// Variable to see if user folders is turned ON
		$enableUserFolders = get_option( LYF_ENABLE_USER_FOLDERS );

		if ( "on" !== $enableUserFolders )
		{
			$output .= '<div id="message" class="updated fade">' . __('<strong>Failed</strong> to upload. Contact your admin about enabling your user folder.') . '</div>';
		}
		else
		{

			//
		    // Next, get the user's upload folder.  Create it if it doesn't exist yet.
		    //

			$userFolder = LYFGetUserUploadFolder( TRUE );

			// If the folder doesn't exist and user folders has been enabled in the
			// admin panel, then create the user folder.
			if ( !is_dir( $userFolder ) )
			{
				$result = LYFCreateUserFolder( $userFolder );
			}

			//
			//	Do the regular work now
			//

			// Check that the user is not trying to upload a file when there's no user
			// folder.
			if ( 0 === strlen( $uploadFolder ) )
			{
				$output .= '<div id="message" class="updated fade">' . __('<strong>Failed</strong> to upload. You need to create and choose a subfolder to upload to.') . '</div>';
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
					$output .= '<div id="message" class="updated fade">' . __('<strong>Failed</strong> to upload. You have already uploaded your size quota.') . '</div>';
					$canUpload = FALSE;
				}
			}

			if ( $canUpload )
			{
				// Now, tack on the folder they want to upload to.
				$output .= LYFUploadFiles( $uploadFolder );
			}
		}
	}

	$action_url = $_SERVER['REQUEST_URI'];
	$noonce = wp_nonce_field('filez-nonce', "_wpnonce", true, false );

	$createFolder = ( FALSE !== stripos( $options, 'create_folder' ) );
	$showFileSizeLimits = ( FALSE !== stripos( $options, 'show_size_warnings' ) );

	$output .= "<script>
	// Multiple file selector by Stickman -- http://www.the-stickman.com with thanks to: [for Safari fixes] Luis Torrefranca -- http://www.law.pitt.edu and Shawn Parker & John Pennypacker -- http://www.fuzzycoconut.com [for duplicate name bug] 'neal'
	function MultiSelector( list_target, max ){this.list_target = list_target;this.count = 0;this.id = 0;if( max ){this.max = max;} else {this.max = -1;};this.addElement = function( element ){if( element.tagName == 'INPUT' && element.type == 'file' ){element.name = 'file_' + this.id++;element.multi_selector = this;element.onchange = function(){var new_element = document.createElement( 'input' );new_element.type = 'file';this.parentNode.insertBefore( new_element, this );this.multi_selector.addElement( new_element );this.multi_selector.addListRow( this );this.style.position = 'absolute';this.style.left = '-1000px';};if( this.max != -1 && this.count >= this.max ){element.disabled = true;};this.count++;this.current_element = element;} else {alert( 'Error: not a file input element' );};};this.addListRow = function( element ){var new_row = document.createElement( 'div' );var new_row_button = document.createElement( 'input' );new_row_button.type = 'button';new_row_button.value = 'Remove';new_row.element = element;new_row_button.onclick= function(){this.parentNode.element.parentNode.removeChild( this.parentNode.element );this.parentNode.parentNode.removeChild( this.parentNode );this.parentNode.element.multi_selector.count--;this.parentNode.element.multi_selector.current_element.disabled = false;return false;};new_row.innerHTML = element.value;new_row.appendChild( new_row_button );this.list_target.appendChild( new_row );};};
	</script>";

	$output .= PHP_EOL . $noonce . PHP_EOL;

	if ( $showFileSizeLimits )
	{
		$maxFolderSize = get_option( LYF_USER_USER_FOLDER_SIZE );
		$uploadFolder = LYFGetUserUploadFolder( TRUE );
		$filesSize = LYFGetFolderSize( $uploadFolder );
		$sizeMessage = LYFFormatFileSize( $filesSize );

		$allowedMessage = '';
		if ( 0 == strlen( $maxFolderSize ) )
			$allowedMessage = 'You are allowed to upload as many files as you want.';
		else
			$allowedMessage = sprintf( __("You are allowed to upload up to %s MB in files."), $maxFolderSize );

		$usingMessage = sprintf( __("You are currently using %s."), $sizeMessage );

		$output .= '<p>' . $allowedMessage . '  ' . $usingMessage . '</p>';
	}

	$output .= '<input id="my_file_element" type="file" name="file_1" />
	<div id="files_list">
		<p>';
	$output .= __('Selected Files <small>(You can upload up to 10 files at once)</small>:');
	$output .='</p>
	</div>
	<div><input type="submit"';

	if ( 0 === $folderCount )
		$output .= 'disabled="disabled" ';

	$output .= 'name="upload_user_files" value="';
	$output .= __('Upload Files');
	$output .= '" /></div>
	<script>
		<!-- Create an instance of the multiSelector class, pass it the output target and the max number of files -->
		var multi_selector = new MultiSelector( document.getElementById( \'files_list\' ), 10 );
		<!-- Pass in the file element -->
		multi_selector.addElement( document.getElementById( \'my_file_element\' ) );
	</script>';

	return $output;
}
?>