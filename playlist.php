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
include("includes/mp3act_functions.php");
include_once("includes/sessions.php");
	$dbh = mp3act_connect();
	$tmp = '';
	$query = '';
		
	$items = $_GET['items'] || '';
	$items2 = explode(" ",$items);
	$items = '';

	$type = $_GET['type'] || '';
	$id = $_GET['id'] || '';

	$num = $_GET['num'] || '';

	//$ip= $_SERVER['REMOTE_ADDR'];
	$user= $_SESSION['sess_usermd5'];
	$userid= $_SESSION['sess_userid'];
	$url = $GLOBALS['http_url'].$GLOBALS['uri_path'];
	//if(verifyIP($_SESSION['sess_usermd5'],$_SERVER['REMOTE_ADDR'])){
	$session_mode =$_SESSION['sess_playmode'];
	
if($session_mode=='jukebox'){
	if($type=='song'){
			$query = "SELECT mp3act_artists.artist_name, 
  			mp3act_songs.song_id,
  			mp3act_songs.name,
  			mp3act_songs.type
  			FROM mp3act_songs,mp3act_artists 
  			WHERE mp3act_songs.song_id=$id 
  			AND mp3act_artists.artist_id=mp3act_songs.artist_id";
	}
	elseif($type=='album'){
			$query = "SELECT mp3act_songs.song_id,
			mp3act_artists.artist_name,
			mp3act_songs.name,
			mp3act_artists.prefix,
			mp3act_songs.length,
			mp3act_songs.type 
			FROM mp3act_songs,mp3act_artists 
			WHERE mp3act_artists.artist_id=mp3act_songs.artist_id AND mp3act_songs.album_id=$id ORDER BY mp3act_songs.track";
	}
	elseif($type=='pl'){
			$query = "SELECT mp3act_songs.song_id,
			mp3act_artists.artist_name,
			mp3act_songs.name,
			mp3act_artists.prefix,
			mp3act_songs.length,
			mp3act_songs.type 
			FROM mp3act_songs,mp3act_artists,mp3act_playlist 
			WHERE mp3act_artists.artist_id=mp3act_songs.artist_id AND mp3act_songs.song_id=mp3act_playlist.song_id AND mp3act_playlist.user_id=$userid AND mp3act_playlist.private=0 ORDER BY mp3act_playlist.pl_id"; 
	}
	elseif($type=='artists'){
			foreach($items2 as $item){
				$items .= " mp3act_songs.artist_id=$item OR";
			}
			$items = preg_replace("/OR$/","",$items);
			$query = "SELECT mp3act_songs.song_id,mp3act_artists.artist_name,mp3act_songs.name,mp3act_songs.length,mp3act_songs.type  FROM mp3act_songs,mp3act_artists	WHERE mp3act_artists.artist_id=mp3act_songs.artist_id AND (".$items.") ORDER BY rand()+0 LIMIT $num"; 

	}
	elseif($type=='genre'){
			foreach($items2 as $item){
				$items .= " mp3act_genres.genre_id=$item OR";
			}
			$items = preg_replace("/OR$/","",$items);
			$query = "SELECT mp3act_songs.song_id,mp3act_artists.artist_name,mp3act_songs.name,mp3act_songs.length,mp3act_songs.type  FROM mp3act_songs,mp3act_artists,mp3act_genres,mp3act_albums	WHERE mp3act_albums.album_id=mp3act_songs.album_id AND mp3act_albums.album_genre=mp3act_genres.genre AND mp3act_artists.artist_id=mp3act_songs.artist_id AND (".$items.") ORDER BY rand()+0 LIMIT $num"; 
	}
	elseif($type=='albums'){
			foreach($items2 as $item){
				$items .= " mp3act_songs.album_id=$item OR";
			}
			$items = preg_replace("/OR$/","",$items);
			$query = "SELECT mp3act_songs.song_id,
			mp3act_artists.artist_name,mp3act_songs.name,mp3act_songs.length,mp3act_songs.type  FROM mp3act_songs,mp3act_artists WHERE mp3act_artists.artist_id=mp3act_songs.artist_id AND (".$items.") ORDER BY rand()+0 LIMIT $num"; 
	}
	elseif($type=='all'){
			$query = "SELECT mp3act_songs.song_id,
			mp3act_artists.artist_name,mp3act_songs.name,mp3act_songs.length,mp3act_songs.type  FROM mp3act_songs,mp3act_artists WHERE mp3act_artists.artist_id=mp3act_songs.artist_id ORDER BY rand()+0 LIMIT $num"; 
	}
	
	$result = mysqli_query($dbh, $query) or die('Query failed: ' . mysqli_error($dbh));
  	header("content-type:text/xml;charset=utf-8");
  	$tmp  ="<?xml version='1.0' encoding='UTF-8'?>\n";
	$tmp .="<playlist version='1' xmlns='http://xspf.org/ns/0/'>\n";
	$tmp .="\t<trackList>\n";
	while($row = mysqli_fetch_array($result)) {
	$tmp .="\t\t<track>\n";
	$tmp .="\t\t\t<title>" . $row['name'] . "</title>\n";
	$tmp .="\t\t\t<creator>" . $row['artist_name'] . "</creator>\n";
	$tmp .="\t\t\t<location>playstream.php?i=".$row["song_id"]."&amp;u=" . $_SESSION[sess_usermd5] . "&amp;b=0&amp;s=s</location>\n";
	$tmp .="\t\t\t<meta rel=\"type\">" . $row['type'] . "</meta>\n";
	$tmp .="\t\t</track>\n";
	}
	$tmp .="\t</trackList>\n";
	$tmp .="</playlist>\n";
	echo $tmp;
	// *****Creamos el archivo
	$archivo="playlist.xml";
   	$fp=fopen($archivo,"w");
   	fwrite($fp,$tmp);
   	fclose($fp);
   	// *****liberamos memoria
	//mysqli_free_result($res);
	//cerramos la conexión
	//mysqli_close($cnx);
}
?>
