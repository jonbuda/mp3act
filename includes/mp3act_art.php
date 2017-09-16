<?php
/*
 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License v2
 as published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

Angel Calleja code injection 2008
www.raro.dsland.org 

*/
include_once("mp3act_functions.php"); 
require_once('getid3/getid3.php');
//require('getid3/getid3.php');
require_once("class/Snoopy.class.php");
//require_once ("class/lastfm.class.php");


	
/**
* get_db_art
* returns the album art from the db along with the mime type
*/
function get_db_art($id) {
		$dbh = mp3act_connect();
		$query ='';
		$query = "SELECT mp3act_albums_data.art, 
				mp3act_albums_data.art_mime, 
				mp3act_albums_data.album_id, 
				mp3act_albums.album_name 
				FROM mp3act_albums_data,mp3act_albums 
				WHERE mp3act_albums.album_id=mp3act_albums_data.album_id AND mp3act_albums_data.album_id=$id";
		$result= mysqli_query($dbh, $query) or die('Query failed: ' . mysqli_error($dbh));
		$row = mysqli_fetch_array($result);
		//if (!$results['art']) { return array(); } 

		$data = array('album_name'=>$row['album_name'],'raw'=>base64_decode($row['art']),'mime'=>$row['art_mime']); 
		
		return $data;

} // get_db_art

/**
* get_folder_art()
* returns the album art from the folder of the audio files
* If a limit is passed or the preferred filename is found the current results set
* is returned
*/
function get_folder_art($id) { 
		$dbh = mp3act_connect();
		$query ='';
		$query = "SELECT mp3act_songs.filename,mp3act_albums.album_name 
				FROM mp3act_songs,mp3act_albums 
				WHERE mp3act_songs.album_id=$id 
				AND mp3act_albums.album_id=mp3act_songs.album_id LIMIT 1";
		$result= mysqli_query($dbh, $query) or die('Query failed: ' . mysqli_error($dbh));
		$row = mysqli_fetch_array($result);
		
		$data = array(); 
		
		// See if we are looking for a specific filename 
		$preferred_filename = "folder.jpg";
		// Init a horrible hack array of lameness
		$cache =array(); 
		
			$dir = dirname($row['filename']);

			/* Open up the directory */
	                $handle = @opendir($dir);

            //    	if (!is_resource($handle)) {
			//	Error::add('general',_('Error: Unable to open') . ' ' . $dir); 
			//	debug_event('read',"Error: Unable to open $dir for album art read",'2');
	        //        }

	                /* Recurse through this dir and create the files array */
	                while ( FALSE !== ($file = @readdir($handle)) ) {
				$extension = substr($file,strlen($file)-3,4);

				/* If it's an image file */
				if ($extension == "jpg" || $extension == "gif" || $extension == "png" || $extension == "jp2") { 

					if ($extension == 'jpg') { $extension = 'jpeg'; } 

					// HACK ALERT this is to prevent duplicate filenames
					$full_filename	= $dir . '/' . $file; 
					$index		= md5($full_filename); 

					/* Make sure it's got something in it */
					if (!filesize($dir . '/' . $file)) { continue; } 
					
					if ($file == $preferred_filename) { 
						// If we found the preferred filename we're done, wipe out previous results
						//$handle2 = fopen($full_filename,'rb'); 
						//$image_data = fread($handle2,filesize($full_filename)); 		
						//fclose($handle2); 
						//$data = array('file' => $full_filename, 'raw' => $image_data, 'mime' => 'image/' . $extension);
						$data = array('file' => $full_filename, 'mime' => 'image/' . $extension, 'album_name' => $row['album_name']);
						return $data;
					}
					elseif (!isset($cache[$index])) {
						//$handle2 = fopen($full_filename,'rb'); 
						//$image_data = fread($handle2,filesize($full_filename)); 		
						//fclose($handle2); 
						//$data = array('file' => $full_filename, 'raw' => $image_data, 'mime' => 'image/' . $extension);
						$data = array('file' => $full_filename, 'mime' => 'image/' . $extension, 'album_name' => $row['album_name'] );
					}
				
					$cache[$index] = '1'; 
				
				} // end if it's an image
				
			} // end while reading dir
			@closedir($handle);
			
		return $data;
		

} // get_folder_art
	
/*!
	@function get_id3_art
	@discussion looks for art from the id3 tags
*/
function get_id3_art($id) { 

		// grab the songs and define our results
		$dbh = mp3act_connect();
		$query ='';
		$query = "SELECT mp3act_songs.filename,mp3act_albums.album_name 
				FROM mp3act_songs,mp3act_albums 
				WHERE mp3act_songs.album_id=$id 
				AND mp3act_albums.album_id=mp3act_songs.album_id";
		$result= mysqli_query($dbh, $query) or die('Query failed: ' . mysqli_error($dbh));

		$data = array(); 

		// Foreach songs in this album
		while($row = mysqli_fetch_array($result)) { 
			// If we find a good one, stop looking
		        $getID3 = new getID3();
			 $id3 = $getID3->analyze($row['filename']); 

			if ($id3['format_name'] == "WMA") { 
				$image = $id3['asf']['extended_content_description_object']['content_descriptors']['13'];
				$data = array('song'=>$row['filename'],'raw'=>$image['data'],'mime'=>$image['mime'],'album_name'=>$row['album_name']);
			}
			elseif (isset($id3['id3v2']['APIC'])) { 
				// Foreach incase they have more then one 
				foreach ($id3['id3v2']['APIC'] as $image) { 
					$data = array('song'=>$row['filename'],'raw'=>$image['data'],'mime'=>$image['mime'],'album_name'=>$row['album_name']);
				} 
			}

			
		} // end while

		return $data;

} // get_id3_art

/**
 * img_resize
 * this automaticly resizes the image for thumbnail viewing
 * only works on gif/jpg/png this function also checks to make
 * sure php-gd is enabled
 */
function img_resize($image,$size,$type,$album_id) {


	$image = $image['raw'];

	if (!function_exists('gd_info')) { return false; }

	/* First check for php-gd */
	$info = gd_info();

	if ( ($type == 'jpg' OR $type == 'jpeg') AND !$info['JPG Support']) {
		return false;
	}
	elseif ($type == 'png' AND !$info['PNG Support']) {
		return false;
	}
	elseif ($type == 'gif' AND !$info['GIF Create Support']) {
		return false;
	}

	$src = imagecreatefromstring($image);
	
	if (!$src) { 
		return false; 
	} 

	$width = imagesx($src);
	$height = imagesy($src);

	$new_w = $size['width'];
	$new_h = $size['height'];

	$img = imagecreatetruecolor($new_w,$new_h);
	
	if (!imagecopyresampled($img,$src,0,0,0,0,$new_w,$new_h,$width,$height)) { 
		return false;
	}

	ob_start(); 

	// determine image type and send it to the client
	switch ($type) {
		case 'jpg':
		case 'jpeg':
			imagejpeg($img,null,100);
			break;
		case 'gif':
			imagegif($img);
			break;
		case 'png':
			imagepng($img);
			break;
	}

	// Grab this image data and save it into the thumbnail
	$data = ob_get_contents(); 
	ob_end_clean();

	// If our image create failed don't save it, just return
	if (!$data) { 
		return $image;
	}
	return $data; 

} // img_resize
function find_art($id) { 
		$dbh = mp3act_connect();
		$query ='';
		$query = "SELECT mp3act_albums.album_name, 
				mp3act_albums.artist_id, 
				mp3act_artists.artist_id, 
				mp3act_artists.artist_name 
				FROM mp3act_albums,mp3act_artists 
				WHERE mp3act_albums.album_id=$id AND mp3act_artists.artist_id=mp3act_albums.artist_id";
		$qry_result= mysqli_query($dbh, $query) or die('Query failed: ' . mysqli_error($dbh));
		$options = mysqli_fetch_array($qry_result);
		//$artist = $options['artist_name'];
		//$album = $options['album_name']; 
		//$key =str_replace(" ", "+", $artist."&album=".$album);
		//$key =str_replace(" ", "+", $artist."&album=".$album);
		//$url="http://www.slothradio.com/covers/?adv=&artist=".$key;
		
		/*$url_web = fopen ($url, "r");
		if (!$url_web) { echo "<p>Error obteniendo codigo fuente de la web.\n"; exit; }
   		$texto=stristr(stream_get_contents($url_web),"<!-- RESULT LIST START -->");
		fclose($url_web); 
		$malo= strlen($texto) - strripos($texto,"<!-- RESULT LIST END -->");
   		$texto2= substr($texto,-$malo,strripos($texto,"<!-- RESULT LIST END -->"));
   		$url2 =str_replace($texto2, "", $texto);
   		return $url2;
		*/
		
		$images = array();
		//$data =array();
		
		$images = slothradio($options,$id);
		/*$i=0;
		foreach ($final_results as $key=>$value) { 
			$i++; 
			$result = $final_results[$key]; 
		
			// Rudimentary image type detection, only JPG and GIF allowed.
			if (substr($result, -4 == '.jpg')) {
				$mime = "image/jpeg";
			}
			elseif (substr($result, -4 == '.gif')) { 
				$mime = "image/gif";
			}
			elseif (substr($result, -4 == '.png')) { 
				$mime = "image/png";
			}
			else {
				continue;
			}
				$data['mime']	= $mime;
				$data['url'] 	= $result;		
				if ($i >= 1) { $images[] = $data; } 
		} // end foreach
*/
		return $images;
} //end find_art

function slothradio($options,$id){
		$artist = $options['artist_name'];
		$album = $options['album_name']; 
		$key =str_replace(" ", "+", $artist."&album=".$album);
		$url="http://www.slothradio.com/covers/?adv=&artist=".$key;
		$snoopy = new Snoopy;
		$snoopy->fetch($url);
		$results = $snoopy->results;

/*		$opciones = array(
  			'http'=>array(
    		'method'=>"GET",
    		'header'=>"Accept-language: en\r\n"
   			)
		);
		$contexto = stream_context_create($opciones);
		//$results = fopen($url, 'r', false, $contexto);
		$results = readfile($url, false, $contexto);
		//file_get_contents($results);
		//fpassthru($results);
		//fclose($results);
*/
		$images = array();
		$image = array();
		$data = parseHtml($results);
//		$fp = fopen("data.txt", "w");
		for ($i=0; $i<=count($data['IMG']); $i++) {
		$url_image = str_replace('"','',$data['IMG'][$i]['SRC']);
		if (substr($url_image,0,10) == 'http://ecx') {
				if (substr($url_image, -4 == '.jpg')) {
					$mime = "image/jpeg";
				}
				elseif (substr($url_image, -4 == '.gif')) { 
					$mime = "image/gif";
				}
				elseif (substr($url_image, -4 == '.png')) { 
					$mime = "image/png";
				}
				else {
					continue;
				}
				//$handle = fopen($url_image, "rb");
				//$contents = stream_get_contents($handle);
				//fclose($handle);
				$image['url'] 	= $url_image;
				$image['mime']	= $mime;
				$image['id'] = $id;
				$images[] = $image;
				
//			fwrite($fp,$url_image.'_____'.$mime);
			}
		}
//		fclose($fp);
return $images;

} // end slothradio
/*
* parseHtml
* Author: Carlos Costa Jordao
* Email: carlosjordao@yahoo.com
*
* My notation of variables:
* i_ = integer, ex: i_count
* a_ = array, a_html
* b_ = boolean,
* s_ = string
*
* What it does:
* - parses a html string and get the tags
* - exceptions: html tags like <br> <hr> </a>, etc
* - At the end, the array will look like this:
* ["IMG"][0]["SRC"] = "xxx"
* ["IMG"][1]["SRC"] = "xxx"
* ["IMG"][1]["ALT"] = "xxx"
* ["A"][0]["HREF"] = "xxx"
*
*/ 
function parseHtml($s_str) {
			$i_indicatorL = 0;
			$i_indicatorR = 0;
			$s_tagOption = "";
			$i_arrayCounter = 0;
			$a_html = array();
// Search for a tag in string
while( is_int(($i_indicatorL=strpos($s_str,"<",$i_indicatorR))) ) {
	// Get everything into tag...
	$i_indicatorL++;
	$i_indicatorR = strpos($s_str,">", $i_indicatorL);
	$s_temp = substr($s_str, $i_indicatorL, ($i_indicatorR-$i_indicatorL) );
	$a_tag = explode( ' ', $s_temp );
	// Here we get the tag's name
	list( ,$s_tagName,, ) = each($a_tag);
	$s_tagName = strtoupper($s_tagName);
	// Well, I am not interesting in <br>, </font> or anything else like that...
	// So, this is false for tags without options.
	$b_boolOptions = is_array(($s_tagOption=each($a_tag))) && $s_tagOption[1];
	if( $b_boolOptions ) {
		// Without this, we will mess up the array
		$i_arrayCounter = (int)count($a_html[$s_tagName]);
		// get the tag options, like src="htt://". Here, s_tagTokOption is 'src' and s_tagTokValue is '"http://"'

		do {
			$s_tagTokOption = strtoupper(strtok($s_tagOption[1], "="));
			$s_tagTokValue = trim(strtok("="));
			$a_html[$s_tagName][$i_arrayCounter][$s_tagTokOption] =
			$s_tagTokValue;
			$b_boolOptions = is_array(($s_tagOption=each($a_tag))) &&
			$s_tagOption[1];
		} while( $b_boolOptions );
	}
}
return $a_html;
} //end parse HTML

function insert_art($k, $id, $m) {
		$dbh = mp3act_connect();
		$url=urldecode($k);
		$mime=urldecode($m);
		$snoopy = new Snoopy;
		$snoopy->fetch($url);
		$data =base64_encode($snoopy->results);
       // Check for PHP:GD and if we have it make sure this image is of some size
   	/*if (function_exists('ImageCreateFromString')) {
		$im = @ImageCreateFromString($image);
		if (@imagesx($im) == 1 || @imagesy($im) == 1 && $im) {
           	return false;
	       	}
		} // if we have PHP:GD

		// Default to image/jpg as a guess if there is no passed mime type
*/		
        $query = "REPLACE INTO mp3act_albums_data SET art= \"$data\" , art_mime=\"$mime\", album_id=$id";
	    mysqli_query($dbh, $query) or die('Query failed: ' . mysqli_error($dbh));
		//return $url."-".$id."-".$mime;
		return $data;
}// end insert_art

?>
