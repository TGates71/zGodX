<?php
function zGodX_main_data(){
      $end ='';

			$user_array = ctrl_users::GetUserDetail();
			$user_x  = $user_array['username'];
			$user_x_id = $user_array['userid'];
      global $zdbh;

if ($user_x == "zadmin" || $user_x_id ==1){ #If zadmin we get everything
$sql = "SELECT * FROM x_vhosts WHERE vh_deleted_ts IS NULL ORDER BY vh_name_vc ASC";
}else{ #Not zadmin, we only get resller account info
$sql = "SELECT * FROM x_vhosts WHERE vh_acc_fk='" .$user_x_id. "' AND vh_deleted_ts IS NULL ORDER BY vh_name_vc ASC";
} #Endif
			$listdomains  = $zdbh->query($sql);
			$listdomains->setFetchMode(PDO::FETCH_ASSOC);
			$rowdomains   = $listdomains->fetch();
      $totaldomains =$zdbh->query($sql)->rowCount();

if(isset($_POST['inDomain'])){
	//if($_GET['a']=='show'){
	
	//if (!empty($_POST)) {
	//$indom ='';
	// } else {
		$indom = $_POST['inDomain'];
		//}
		$sql = "SELECT vh_acc_fk FROM x_vhosts WHERE vh_name_vc ='".$indom."'";
			$listdomain1  = $zdbh->query($sql);
			$listdomain1->setFetchMode(PDO::FETCH_ASSOC);
			$rowdomain1   = $listdomain1->fetch();
		
		$sql2 = "SELECT ac_user_vc FROM x_accounts WHERE ac_id_pk ='".$rowdomain1['vh_acc_fk']."'";
			$listdomain2  = $zdbh->query($sql2);
			$listdomain2->setFetchMode(PDO::FETCH_ASSOC);
			$rowdomain2   = $listdomain2->fetch();
		 $report_to_show = "modules/webalizer_stats/stats/" . $rowdomain2['ac_user_vc'] . "/" . $indom . "/index.html";
	//}
}

if($totaldomains>0){
$end.='<em>zGodX &raquo; <b>Webalizer Stats</b></em><br><br>';

$end.="<form action=\"" .GetNormalModuleURL(GetFullURL()). "\" method=\"post\">
<INPUT type=\"hidden\" name=\"getMain\" value=\"".$_POST['getMain']."\">
<table class=\"zform\">
<tr>
<td><strong>Domain:</strong></td>
<td><select name=\"inDomain\" id=\"inDomain\">
<option value=\"\">-- Select a domain --</option>";

do {
$end.= "<option value=\"" .$rowdomains['vh_name_vc']."\">" .$rowdomains['vh_name_vc']. "</option>";

 } while($rowdomains = $listdomains->fetch());

$end.= "</select></td>";
//<td><input type=\"submit\" name=\"Submit\" value=\"Display\">";
$end.='<td>&nbsp;<button class="button-loader btn btn-default" type="submit" name="submit" id="button" value="Display">Display</button>
</td>
</tr>
</table>
</form>';

$end.= "<script type=\"text/javascript\">
function autoIframe(frameId){
try{
frame = document.getElementById(frameId);
innerDoc = (frame.contentDocument) ? frame.contentDocument : frame.contentWindow.document;
objToResize = (frame.style) ? frame.style : frame;
objToResize.height = innerDoc.body.scrollHeight + 10;
}
catch(err){
window.status = err.message;
}
}
</script>
";

//if ((isset($_GET['a'])) && ($_GET['a'] == "show")) {
if(isset($_POST['inDomain']) && $_POST['inDomain']!=''){
      $end.= "<br><h2>Domain traffic report</h2><iframe height=\"600\" width=\"100%\" allowtransparency=\"\" src=\"" . $report_to_show . "\" title=\"Domain traffic report\" frameborder=\"0\" scrolling=\"auto\"></iframe>";
    }
} else {
   $end.='
   <em>zGodX &raquo; <b>Webalizer Stats</b></em><br><br>
   <b>You currently do not have any domains setup on your account.</b>';
}

return $end;
}
?>