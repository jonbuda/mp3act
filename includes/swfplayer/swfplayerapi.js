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
/*-------------------------------------------------------------------
Player javascript API
-------------------------------------------------------------------*/
// some variables to save
var player_template;
var currentPosition;
var currentRemaining;
var currentVolume;
var currentItem;
var currentState;
var currentLoad;
var currentXsize;
var currentYsize;


function sendEvent(swf,typ,prm) { 
	thisMovie(swf).sendEvent(typ,prm); 
};
//------------------controls play/pause (0:ready/paused, 1:loading, 2:playing, 3:finished)
function playtoggle() {
	flag=(! flag);
	if (flag) { stat=1; } else {stat=0; }
	document.getElementById("buttonplay").src=imgDat[stat].src;
};

var flag=false;
var stat=0;
var imgURL;
var imgDat;
function preload(imgStr) {
	imgURL = new Array();
	imgURL = imgStr.split(',');
	imgDat = new Array();
	for(i=0; i<imgURL.length; i++) { 
		imgDat[i] = new Image();
		imgDat[i].src = imgURL[i];
	}
};

/*function loadFile(swf,obj) {
	thisMovie(swf).loadFile(obj); 
	sendEvent(swf,'playpause');
	playtoggle();
};

function addItem(swf,obj,idx) {
	thisMovie(swf).addItem(obj,idx);
	sendEvent(swf,'item');
};

function removeItem(swf,idx) {
	thisMovie(swf).removeItem(idx);
	sendEvent(swf,'item');
};*/
function clearPlayer(p_element_id) {
document.getElementById('jwplaylist').src = "playlist.php";
sendEvent("jvplayer","stop");
document.getElementById("buttonplay").src=imgDat[0].src;
document.getElementById("title").innerHTML = "<b>No Songs Playing</b>";
document.getElementById("author").innerHTML = "";
loadPlayer(p_element_id,"playlist.php"); //%3Ftype%3D"+type+"%26id%3D"+id
};
function thisMovie(movieName) {
	if(navigator.appName.indexOf("Microsoft") != -1) {
		return window[movieName];
	} else {
		return document[movieName];
	}
};
//-------------end controls

//-------------item info
function getUpdate(typ,pr1,pr2,swf) { 
	if(typ == "time") { currentPosition = pr1; pr2 == undefined ? null: currentRemaining = Math.round(pr2); }
	else if(typ == "volume") { currentVolume = pr1; }//setMsgText("Current Volume "+ pr1);} 
	else if(typ == "item") { currentItem = pr1; setTimeout("getItemData('jvplayer',currentItem)",100);}
	else if(typ == "state") { currentState = pr1; if(currentState==3) { playtoggle(); } if(currentState==2){document.getElementById("buttonplay").src=imgDat[1].src;} }
	else if(typ == "load") { currentLoad = pr1; }
	else if(typ == "size") { currentXsize = "X=" + pr1; pr2 == undefined ? null: currentYsize = "Y=" + Math.round(pr2); } 
	
	var tmp = document.getElementById("pid"); if ((tmp)&&(swf != "null")) { tmp.innerHTML = "(player id: <i><b>"+swf+"</b></i>)"; } 
	var tmp = document.getElementById("time"); if (tmp) { tmp.innerHTML = "<b>Time:</b> " + currentPosition + "&nbsp;&nbsp;<b>Remaining:</b> " + currentRemaining; } 
	var tmp = document.getElementById("volume"); if (tmp) { tmp.innerHTML = "<b>Volume:</b> " + currentVolume; } 
	var tmp = document.getElementById("item"); if (tmp) { tmp.innerHTML = "<b>Item:</b> " + currentItem; } 
	var tmp = document.getElementById("state"); if (tmp) { tmp.innerHTML = "<b>State:</b> " + currentState + "&nbsp;&nbsp; (0:ready/paused, 1:loading, 2:playing, 3:finished)"; } 
	var tmp = document.getElementById("load"); if (tmp) { tmp.innerHTML = "<b>Load:</b> " + currentLoad; }
	var tmp = document.getElementById("size"); if (tmp) { tmp.innerHTML = "<b>Size:</b> " + currentXsize + ", " + currentYsize; }
};
function getItemData(swf,idx) {
	var obj = thisMovie(swf).itemData(idx);
	var tmp = document.getElementById("file"); if (tmp) { tmp.innerHTML = "<b>File:</b> " + obj["file"]; } 
	var tmp = document.getElementById("title"); if (tmp) { tmp.innerHTML = "<b>Title:</b> " +  obj["title"]; } 
	var tmp = document.getElementById("link"); if (tmp) { tmp.innerHTML = "<b>Link:</b> " + obj["link"]; } 
	var tmp = document.getElementById("type"); if (tmp) { tmp.innerHTML = "<b>Type:</b> " + obj["type"]; } 
	var tmp = document.getElementById("id"); if (tmp) { tmp.innerHTML = "<b>Id:</b> " + obj["id"]; } 
	var tmp = document.getElementById("image"); if (tmp) { tmp.innerHTML = "<b>Image:</b> " + obj["image"]; } 
	var tmp = document.getElementById("author"); if (tmp) { tmp.innerHTML = "<b>Author:</b> " + obj["author"]; } 
	var tmp = document.getElementById("captions"); if (tmp) { tmp.innerHTML = "<b>Captions:</b> " + obj["captions"]; } 
	var tmp = document.getElementById("audio"); if (tmp) { tmp.innerHTML = "<b>Audio:</b> " + obj["audio"]; } 
	var tmp = document.getElementById("start"); if (tmp) { tmp.innerHTML = "<b>Start:</b> " + obj["start"]; }  
	var tmp = document.getElementById("category"); if (tmp) { tmp.innerHTML = "<b>Category:</b> " + obj["category"]; } 
	var tmp = document.getElementById("date"); if (tmp) { tmp.innerHTML = "<b>Date:</b> " + obj["date"]; }  
};
//---------------end item  info

// -------------- Load PLayer

function loadPlayer(p_element_id,TheFile) {
//on loading make visible player div's
        document.getElementById(p_element_id).innerHTML = "";
   		document.getElementById(p_element_id).style.visibility="visible";
        document.getElementById(p_element_id).innerHTML = player_template;        
        //document.getElementById(p_element_id).scrollIntoView();
    var s = new SWFObject("includes/swfplayer/mediaplayer.swf","jvplayer","0","0","7");
	s.addParam("allowfullscreen","false");
	s.addParam("allowscriptaccess","always");
	s.addVariable("javascriptid","jvplayer");
	s.addVariable("enablejs","true");
	s.addVariable("width","150");
	s.addVariable("file",TheFile);
	s.addVariable("height","0");
	s.addVariable("displayheight","0");
	s.addVariable("autostart","false");
	s.addVariable("shownavigation","false");
	s.addVariable("thumbsinplaylist","false");
	s.write(p_element_id);
};

function initPlayer(p_element_id) {
	preload("img/play_big.gif, img/pause_big.gif");
	loadPlayer(p_element_id,"playlist.php");
};