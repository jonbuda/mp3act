<?php

header("Content-Type: text/css");
include_once("../includes/mp3act_functions.php");
include_once("../includes/sessions.php");
mp3act_connect();
/*****************************************
*  Main Style Sheet for: mp3act music player
*  Date Created: 3-12-2005
*  Author: Jon Buda (http://mp3act.net)
*  Filename: mp3act.css
*****************************************/
$theme_id = (isset($_SESSION['sess_theme_id']) ? $_SESSION['sess_theme_id'] : 1);
$query = "SELECT * FROM mp3act_themes WHERE theme_id=$theme_id";
$result = mysql_query($query);
$row = mysql_fetch_array($result);

$dark = $row['color1'];
$medium = $row['color2'];
$light = $row['color3'];
$lightest = $row['color4'];
$contrast = $row['color5'];
?>

body{
	padding: 22px;
	margin:0;
	color: #333;
	background: #F0F0F0;
	text-align: center;
	font: 65% Verdana, Sans-serif;
}

h2{
	font-size: 100%;
	margin:0;
	padding: 0 0 5px 0;

}
ul{
	list-style-type: none;
}
a{
	color: blue;
}

a:hover{
	/*color: #fff;
	background: #F21518;
	text-decoration: none;*/
	color: #F21518;
}
img{
	border: 0;
}
input,select{
	border: 1px solid #ccc;
	border-color: #aaa #ccc #ccc #aaa;
	background: #f3f3f3;
	color: #555;
	font-size: 100%;
	padding: 2px 3px;
	vertical-align: middle;
	
}
input.check{
  border:0;
  padding:0;
  background: transparent;
}
select{
	padding: 2px 0 2px 3px;
}
input:focus{
	border: 1px solid #999;
	background: #FBF9D3;
	color: #000;
	border-color: #777 #bbb #bbb #777;
}

input.btn,input.redbtn{
	background: #244A79;
	color: #fff;
	padding: 2px;
	border-color: #0E2F58;
	font: normal 10px sans-serif;
}
input.redbtn{
	background: #F21518;	
}
input.redbtn:hover{
	background: #BE0D0F;
}
input.btn:hover{
	background: #0E2F58;
}

input.btn2{
	font-weight: bold;
	padding: 2px;
}

input.btn2:hover{
	background: #eee;
	border-color: #888;
	color: #222;
}

.left{
	float: left;
}
.right{
	float: right;
}
.center{
	text-align: center;
}
.clear{
  clear: both;
}
.error{
	color: #E63838;
	font-weight: bold;
}
p#error{
	color: #f20000;
	font-weight: bold;
}
#breadcrumb{
	height: 14px;
	padding:2px 0 0 0;
}
#breadcrumb span{
position: relative;
}
#breadcrumb span:hover ul{
	display: block;
}
#breadcrumb ul{
	z-index: 5;
	border: 1px solid #333;
	display: none;
	top:12px;
	left:0;
	position: absolute;
	background: transparent url("../img/libg.png");
	color: #fff;
	margin:-1px 0 0 0;
	padding:0;
	width: 150px;
	
}
#breadcrumb ul#letters{
	left: -55px;
	width: auto;
}
#breadcrumb span{
	padding: 0;
	margin:0;
}
#breadcrumb ul li{
width: 100%;
	padding:0;
	margin:0;
	z-index: 6;
}
#breadcrumb ul li a{
	display: block;
	padding: 2px 4px;
	color: #fff;
	margin:0;
	z-index: 5;
	text-decoration: none;
	font-weight: normal;
	font-size: 90%;
}

#breadcrumb ul#letters li{
	float: left;

}
#breadcrumb ul#letters li a{
	float: left;
	
}
#breadcrumb ul li a:hover{
	background: #FCF7A5;
	color: #000;
}
#breadcrumb ul#letters li a:hover{
	background: #FCF7A5;
	color: #000;
}
#topinfo{
	
	font-size: 90%;
	color: #666;
	text-align: left;
	padding: 0 0 4px 0;
	
}
p.pad{
	padding: 0px 8px;
}

#wrap{
	background: #fff;
	border: 1px solid #ccc;
	text-align: left;
	padding: 0px;
	margin:0;
	position: relative;
}

#header{
	position: relative;
	background: <?php echo $dark; ?>;
	height: 50px;
	color: #fff;
	padding: 8px 0 0px 15px;
}
#header #controls{
	float: right;
	background: transparent;
	height: 48px;
	margin-right: 8px;
	width: 48%;
	font-size: 90%;
	line-height: 1.1em;
	color: #fff;
}

#header #controls .buttons{
	float: left;
	margin: 3px 5px 0 5px;
}
#header #controls .current{
	float: left;
	margin-top: 3px;
	
}
#header h1{
	color: <?php echo $lightest; ?>;
	padding: 0;
	margin:0;
	font-size: 150%;
}
ul#nav{
	position: absolute;
	bottom:0;
	list-style-type: none;
	margin:0;
	padding:0;
}
ul#nav li{
	float: left;
	margin-right: 5px;
}
ul#nav li a{
	display: block;
	background: <?php echo $medium; ?>;
	padding: 4px 5px;
	color: #ccc;
	text-decoration: none;
	margin:0;
}
ul#nav li a:hover{
	background: <?php echo $light; ?>;
	color: #fff;
}
ul#nav li a.c{
	background: #fff;
	color: <?php echo $contrast; ?>;
	font-weight: bold;
}
#loading{
	display: none;
	position: absolute;
	top: 80px;
	color: #78B855;
	padding: 10px;
	background: #CCFF99;
	border: 1px solid #78B855;
	z-index: 4;
	left: 30%;
}
#loading h1{
  font-size: 140%;
}
#left{
	float: left;
	width: 48%;
	margin: 15px 0 25px 15px;
}
#right{
	float: right;
	width: 45%;
	margin: 15px 15px 25px 0;

}
.box{
	background: #E0E0E0;
	border: 1px solid #ccc;
	padding: 0 0 8px 0;
	position: relative;
}
#box_extra{
	display: none;
	z-index: 2;
	position: absolute;
	top: 25px;
	left:5%;
	width: 90%;
	height: 120px;
	background: transparent url("../img/libg.png");
	color: #fff;
	font-size: 110%;
	padding: 5px;
}
#box_extra h2{
	font-size: 120%;
}
#box_extra input{
	background: #333;
	border-color: #999;
	color: #fff;
}
.box ul{
	margin: 0 10px;
	padding: 0px 0px;
	background: #f3f3f3;
	clear: both;
}
.box ul li{
	padding: 2px 0 1px 4px;
	border: 1px solid transparent;
	border-width: 1px 0px 1px 0px;
	position: relative;
	background: #f3f3f3; 
}

.box ul li.alt{
	background: #DEE6EC;
}
.box ul li span.user{
	float: left;
	width: 200px;
}
.box ul li small{
	color: #888;
	letter-spacing: -1px;
}

.box ul li span.links a{
	text-decoration: underline;
	color: blue;
}
.box ul li span.links a:hover{
	color: red;
}
.box ul li:hover{
	background: #FCF7A5;
	color: #000;
	border-color: #999;
}
.box ul li.currentplay{
	background: #96D1EF;
	color: #000;
	border-color: #666;
}

.box ul li p{
	z-index:5;
	display: none;
	position: absolute;
	top: 7px;
	font-size: 90%;
	padding: 2px;
	right: 15px;
	width: 145px;
	background: transparent url("../img/libg.png");
	color: #fff;
}

.box ul li:hover p{
	display: block;
}

.box ul li a{
	text-decoration: none;
	color: #333;
}
.box ul li a:hover{
	color: #000;
}
.box ul#letters{
	height: 18px;
	padding-left: 5px;
}

.box ul#letters li{
	float: left;
	padding: 2px 2px;
	background: none;
	border: 1px solid transparent;
}

.box ul#letters li:hover{
	background: #FCF7A5;
	border-color: #999;
}

.box p{
	padding: 0 10px;
	margin: 8px 0 4px 0;

}
.box p img{
	display: block;
	width: 60px;
	float: right;
	padding: 2px;
	background: #f3f3f3;
	border: 1px solid #999;
	margin-bottom: 4px;	
}
.box img#bigart{
	display: none;
	position: absolute;
	z-index: 2;
	background: #f3f3f3;
	padding: 3px;
	border: 1px solid #666;
	top:10px;
	right: 80px;
}
.box p img:hover{
	border-color: #555;
	cursor: pointer;
}

.box .head{
	padding: 4px;
	background: #ccc;
}
.box .head a{
	background: #244A79;
	color: #fff;
	padding: 2px;
	text-decoration: none;
	font: normal 9px sans-serif;
}
.box .head a:hover{
	background: #0E2F58;
}
.box .head a.red{
	background: #F21518;
}	
.box .head a.red:hover{
	background: #BE0D0F;
}
.box .head h2{
	font-size: 120%;
	padding: 0;
}
.box h3{
	padding: 0 0 0 0px;
	margin: 0 10px 0 10px;
	font-size: 120%;
	border-bottom: 1px solid #ccc;
}

.loginbox{
	width: 250px;
	margin:0 auto;
	background: #fff;
	border: 1px solid #ccc;
	text-align: left;
}
.loginbox p{
	padding: 8px 15px;
	margin:0;
}
.noborder{
	background: transparent;
	border:0;
}
