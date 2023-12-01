<?php
function zGodX_main_data(){
			$user_array = ctrl_users::GetUserDetail();
			$user_x  = $user_array['username'];
			$user_x_id = $user_array['userid'];
      global $zdbh;
$end='';
      
#get forwards
if ($user_x == "zadmin" || $user_x_id ==1){ #If zadmin we get everything
$sql = "SELECT * FROM x_aliases WHERE al_deleted_ts IS NULL";
}else{ #Not zadmin, we only get resller account info
$sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk='" .$user_x_id. "' OR ac_id_pk='" .$user_x_id. "' AND ac_deleted_ts IS NULL";
      $list     = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			
      $total = $zdbh->query($sql)->rowCount();
$client=array();
 if($total <> 0){
	while($row = $list->fetch()){
	$client[] = $row['ac_id_pk'];
    }
 }
$sql = "SELECT * FROM x_aliases WHERE al_acc_fk IN('".join("','", $client)."') AND al_deleted_ts IS NULL";
} #Endif
				$list = $zdbh->query($sql);
				$list->setFetchMode(PDO::FETCH_ASSOC);

$row  = $list->fetch();

$total = $zdbh->query($sql)->rowCount();
if ($total > 0){

$end.='
<em>zGodX &raquo; <b>Aliases</b></em><br><br>
<table class="table table-striped">
            	<tr>
                	<th colspan="1">Account</th>
                    <th colspan="1">Virtual Email</th>
                    <th colspan="1">Real Email</th>
                    <th colspan="1">Created</th>
                    <th colspan="1"><center>Actions</center></th>
                </tr>';
  do{
    
	$sql = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['al_acc_fk'] ."'";
	$listforwarderacc = $zdbh->query($sql);
	$listforwarderacc->setFetchMode(PDO::FETCH_ASSOC);
	$rowforwarderacc  = $listforwarderacc->fetch();
	
$end.='    			<tr>
    				<td><a href="javascript:void(0)" onClick="document.frmMain.getMain.value=\'user\'; document.frmMain.getUser.value\''.$rowforwarderacc['ac_id_pk'].'\'; document.frmMain.submit();">'.$rowforwarderacc['ac_user_vc'].'</a></td>
      				<td>'.$row['al_address_vc'].'</td>
      				<td>'.$row['al_destination_vc'].'</td>
      				<td>'.date("M d, Y",$row['al_created_ts']).'</td>
      				<td><center><a title="Link to Delete Alias" href="?module=aliases&show=Delete&other='.$row['al_id_pk'].'"><img src="modules/zgodx/images/deletesmall.png" border="0" /></a></center></td>
    			</tr>';
    } while($row = $list->fetch());
    
			$end.= '</table>';

} else { 
$end .= "
<em>zGodX &raquo; <b>Aliases</b></em><br><br>
<b>You have no aliases at this time</b>"; 
} 

return $end;
}
?>