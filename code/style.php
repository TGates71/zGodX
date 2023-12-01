<?php
function zGodx_css_js_data() {
$end='';

$end.='
<script language="javascript"> 
function toggle() {
	var ele = document.getElementById("toggleText");
	var text = document.getElementById("displayText");
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "show";
  	}
	else {
		ele.style.display = "block";
		
	}
} 
</script>

<script language="javascript"> 
(function() {
  var ua = navigator.userAgent,
      iStuff = ua.match(/iPhone/i) || ua.match(/iPad/i),
      typeOfCanvas = typeof HTMLCanvasElement,
      nativeCanvasSupport = (typeOfCanvas == \'object\' || typeOfCanvas == \'function\'),
      textSupport = nativeCanvasSupport 
        && (typeof document.createElement(\'canvas\').getContext(\'2d\').fillText == \'function\');
  labelType = (!nativeCanvasSupport || (textSupport && !iStuff))? \'Native\' : \'HTML\';
  nativeTextSupport = labelType == \'Native\';
  useGradients = nativeCanvasSupport;
  animate = !(iStuff || !nativeCanvasSupport);
})();
</script>


<script type="text/javascript">
$(document).ready(function(){
$("#pop_log").click(function(){
  $("#hidden_log").fadeIn(1000);
  positionPopup();
});

$("#close1").click(function(){
	$("#hidden_log").fadeOut(500);
});

$("#close2").click(function(){
	$("#hidden_log").fadeOut(500);
});

});

//position the popup at the center of the page
function positionPopup(){
  if(!$("#hidden_log").is(\':visible\')){
    return;
  } 
  $("#hidden_log").css({
      left: ($(window).width() - $(\'#hidden_log\').width()) / 2,
      //left:240,
      //top: ($(window).width() - $(\'#hidden_log\').width()) / 7,
      top: 100,
      bottom: 20,
      position:\'absolute\'
  });
}

//maintain the popup at center of the page when browser resized
$(window).bind(\'resize\',positionPopup);
</script>
';




$end.='
<style type="text/css">
#toggleText {
margin:0 auto;
width:80%;
color:#FFFFFF;
z-index:1000;
position: fixed;
top:0;
bottom:0;
left:0;
width:100%;
background:#000;
opacity:0.45;
-moz-opacity:0.45;
filter:alpha(opacity=70);
}
#toggleText h1 {
color:#FFFFFF;
}
#wrapper {
   height:100%;
   width: 100%;
   margin: 0;
   padding: 0;
   border: 0;
}
#wrapper td {
   vertical-align: middle;
   text-align: center;
}

#progress {
    width: 120px;
}
.graph {
    width: 120px;
    height: 15px;
    background: rgb(168,168,168);
    position: relative;
    font-size:10px;
}
#barb {
    height: 14px;
    background: rgb(0,162,232);
    color:#fff;
}
#barg {
    height: 14px;
    background: rgb(0,128,0);
    color:#fff;
}
#baro {
    height: 14px;
    background: rgb(255,128,0);
    color:#fff;
}
#barr {
    height: 14px;
    background: rgb(128,0,0);
    color:#fff;
}
#barb p { position: absolute; text-align: center; width: 100%; margin: 0; line-height: 15px; }
#barg p { position: absolute; text-align: center; width: 100%; margin: 0; line-height: 15px; }
#baro p { position: absolute; text-align: center; width: 100%; margin: 0; line-height: 15px; }
#barr p { position: absolute; text-align: center; width: 100%; margin: 0; line-height: 15px; }


/*cpu and ram */
.left {	
	float: left;
}
.buttonx {
	cursor: pointer;
	}
/* zDaemon LOG pop up */
#hidden_log{
	position: absolute;
	padding: 10px;
	background: black;
	color: white;
	min-width: 500px;
	min-height: 100px;
	-webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  border-radius: 5px;
  border: 1px solid green;
  overflow:auto;
  z-index:100;
}

#close1 {
float:right;
position:absolute;
top:10px;
right:10px;
}
#close2 {
float:right;
position:relative;
bottom:10px;
right:10px;
}

</style>';


$end.='<a id="displayText" href="javascript:toggle();"></a>
<div id="toggleText" style="display: none">
<table id="wrapper">
      <tr>
         <td><center><img style="width:100px;height:100px" src="modules/zgodx/images/loading.gif" border="0" /></center></td>
      </tr>
</table>
</div>';



return $end;
}