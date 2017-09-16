<?php
include_once("includes/mp3act_functions.php");

$url = $GLOBALS['http_url'].$GLOBALS['uri_path'];
$album_id = $_GET['id'];

// Decide what size this image is 
switch ($_GET['thumb']) { 
	case '1':
		// This is used by "browse" menu
		$size['height'] = '80';
		$size['width']	= '80';
	break;
	case '2':
		// This is used by "browse" menu thumb
		$size['height']	= '128';
		$size['width']	= '128';
	break;
	default:
		$size['height'] = '275';
		$size['width']	= '275';
	break;
}


		// Attempt to pull art from the database
		$art =get_db_art($album_id);
			if(!$art['raw']) {
				// Attempt to pull art from folder
				$art=get_folder_art($album_id);
				if ($art['file']) { 
					$handle = fopen($art['file'],'rb'); 
					$image_data= fread($handle,filesize($art['file'])); 		
					fclose($handle); 
		//			// verificar si se incluye en el array
					$art['raw'] = $image_data;
		//			$art_data = $art['raw']; 
		//			$art_data = $image_data;
		//			$mime = $art['mime'];
				} else {
					$art = get_id3_art($album_id);
		//			if (isset($art['song'])) {
		//			$mime = $art['mime'];
		//			$art_data = $art['raw'];
		//			}
				}
			}
		$mime = $art['mime'];
		if (!$mime) { 
			header('Content-type: image/gif');
			readfile($url . '/img/blankalbum.gif');
			//break;
		} // else no image
		else {

			// Print the album art
			$data = explode("/",$mime);
			$extension = $data['1'];
			$art_data=$art['raw'];
			if (empty($_REQUEST['thumb'])) { 
				$art_data = $art['raw'];
			}
			else { 
				$art_data = img_resize($art,$size,$extension,$album_id);
			}
			// Send the headers and output the image
   	     	header("Expires: Sun, 19 Nov 1978 05:00:00 GMT"); 
   	     	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
   	     	header("Cache-Control: no-store, no-cache, must-revalidate");
   	     	header("Pragma: no-cache");
			header("Content-type: $mime");
			header("Content-Disposition: filename=" . $art['album_name'] . "." . $extension);	
			echo $art_data;
		}

?>
