<?php
/* =============== about:
 * this should allow a simple file/picture upload, no ajax, no progress bar, just keep it simple style
 * variables needs to be set in the file.php that includes ./lib/php/lib_upload.php like this: (example frontend_useradd.php)

 * =============== usage:
	<?php
		// include upload form
 	 	// 1. get user
		$user = getUser($_REQUEST['selectUserId']);
		// 2. get data-details of user as array
		$data = getDataOfUserID(null,$user);

		// this is just for "tagging" and "title"
		$category = "profilepicture";
		
		// specify a path where your users may store their profile pictures (is set in config.php)
		$settings_profilepicture_upload_dir = "images/profilepictures/";
		
		require ("./lib/php/lib_upload.php");
	?>

WARNING: DO NOT PASS uploadDirectory parameters as url?uploadTo=thisDir
because hackers will overwrite important files (.htaccess redirects) and misuse your site as spam-hoster.
*/

/* ============ PARAMETERS */

/* you can specify if the file should be different from the original filename */ 
global $upload_change_filename;
if(!isset($upload_change_filename))
{
	$upload_change_filename = "";
}

global $upload_maximumFileSize;
if(!isset($upload_maximumFileSize))
{
	$upload_maximumFileSize = 2048;
}

global $upload_allowedExtensions;
if(!isset($upload_allowedExtensions))
{
	$upload_allowedExtensions = array("gif", "jpeg", "jpg", "png");
}

global $settings_profilepicture_dimensions;
if(!isset($settings_profilepicture_dimensions))
{
	$settings_profilepicture_dimensions = "115x115";
}

global $category;
if(!isset($category))
{
	$category = "";
}

global $settings_profilepicture_upload_dir;
if(!isset($settings_profilepicture_upload_dir))
{
	$settings_profilepicture_upload_dir = "/"; // will probably upload in the root dir
}
/* ============ */

if(isset($_REQUEST["category"]))
{
	$category = $_REQUEST["category"];
}

global $settings_current_filename;

$upload_allowedExtensions_string = "";
$target = count($upload_allowedExtensions);
for($i=0;$i<$target;$i++)
{
	$upload_allowedExtensions_string .= "*.".$upload_allowedExtensions[$i].", ";
	$upload_allowedExtensions[] = "image/".$upload_allowedExtensions[$i];
}

$upload_maximumFileSize = $upload_maximumFileSize * 1024;

if($_FILES)
{
	// detected upload request, processing upload, display file

	if(checkExtension($_FILES["file"]["type"]))
	{
		if(($_FILES["file"]["size"] < $upload_maximumFileSize))
		{
			if ($_FILES["file"]["error"] > 0)
			{
				echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
			}
			else
			{
				echo '
				<h4>'.$category.': ';
				if($category == "profilepicture")
				{
					echo 'Please do not upload pictures larger than: '.$settings_profilepicture_dimensions.'px';
				}
				echo '</h4>
				<form action="'.$settings_current_filename.'.php?'.$_SERVER['QUERY_STRING'].'" method="post" enctype="multipart/form-data" rel="external" data-ajax="false">
					<fieldset data-role="controlgroup" data-type="horizontal">
					<div id="'.$category.'_div" class="profilePictureParent" style="border: 1px solid; height: 115px; width: 115px;">
						<img id="'.$category.'" class="profilePicture" src="'.$settings_profilepicture_upload_dir. $_FILES["file"]["name"].'">
					</div>
					<div id="uploadDetails">
						Upload: '. $_FILES["file"]["name"] . '<br>
						Type: ' . $_FILES["file"]["type"] . '<br>
						Size: ' . ($_FILES["file"]["size"] / 1024) . ' kB<br>
						<!-- Temp file: ' . $_FILES["file"]["tmp_name"] . '--><br>
				';

				if (file_exists("upload/" . $_FILES["file"]["name"]))
				{
					echo $_FILES["file"]["name"] . " already exists. ";
				}
				else
				{
					if($upload_change_filename)
					{
						// extract extension
						$ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
						$destination = $settings_profilepicture_upload_dir . $upload_change_filename .".". $ext;
					}
					else
					{
						$destination = $settings_profilepicture_upload_dir . $_FILES["file"]["name"];
					}
					move_uploaded_file($_FILES["file"]["tmp_name"],$destination);
					echo "Stored in: " . $destination;
				}
				echo '
						</div>
					</fieldset>
					<input type="file" name="file" id="file"><br>
					<input type="submit" name="submit" value="upload">
				</form>';
			}
		}
		else
		{
			echo "File exceeds filezie limit of ".$upload_maximumFileSize."kByte.";
		}
	}
	else
	{
		echo "File was not a allowed filetypes: ".$upload_allowedExtensions_string;
	}
}
else
{
	// output upload form
	$showProfilePicture = "";
	if(isset($data))
	{
		if(isset($data['profilepicture']))
		{
			$showProfilePicture = $data['profilepicture'];
		}
	}
	
	$userID = "";
	if(isset($user))
	{
		if(isset($user->id))
		{
			$userID = $user->id;
		}
	}
	echo '
	<h4>'.$category.': ';
	if($category == "profilepicture")
	{
		echo 'Please do not upload pictures larger than: '.$settings_profilepicture_dimensions.'px';
	}
	echo '</h4>
	<form action="'.$settings_current_filename.'.php?'.$_SERVER['QUERY_STRING'].'" method="post" enctype="multipart/form-data" rel="external" data-ajax="false">
		<label for="file">'.$category.':</label>
		<div id="'.$category.'_div" class="profilePictureParent">
			<img id="'.$category.'_div" src="'.$showProfilePicture.'" class="profilePicture"/>
		</div>
		<input type="file" name="file" id="file"><br>
		<input type="submit" name="submit" value="upload">';
	echo '<input type="hidden" name="selectUserId" value="'.$userID.'">
	</form>
	';
}

	function checkExtension($ext)
	{
		$extension = end(explode(".", $_FILES["file"]["name"]));

		$result = false;
		global $upload_allowedExtensions;
		if(in_array($_FILES["file"]["type"], $upload_allowedExtensions))
		{
			if(in_array($extension, $upload_allowedExtensions))
			{
				$result = true;
			}
			else
			{
				$result = false;
			}
		}
		
		return $result;
	}
?>