<?php
function zGodX_main_data(){
      $end ='';

			$user_array = ctrl_users::GetUserDetail();
			$user_x  = $user_array['username'];
			$user_x_id = $user_array['userid'];
      global $zdbh;

if (isset($_POST['getToggleMail'])){ $end.= zToggleMail($_POST['getToggleMail'], $_POST['getToggleMailID']);} 

#get Mailboxes
if ($user_x == "zadmin" || $user_x_id ==1){ #If zadmin we get everything
$sql = "SELECT * FROM x_mailboxes WHERE mb_deleted_ts IS NULL";
}else{ #Not zadmin, we only get resller account info
$sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk='" .$user_x_id. "' OR ac_id_pk='" .$user_x_id. "' AND ac_deleted_ts IS NULL";
$list = $zdbh->query($sql)->execute();
$total = $zdbh->query($sql)->rowCount();
      $lists     = $zdbh->query($sql);
			$lists->setFetchMode(PDO::FETCH_ASSOC);
$client=array();
 if($total <> 0){
	while($row = $lists->fetch()){
	$client[] = $row['ac_id_pk'];
    }
 }
$sql = "SELECT * FROM x_mailboxes WHERE mb_acc_fk IN('".join("','", $client)."') AND mb_deleted_ts IS NULL";
} #Endif
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();
if ($total > 0){
$end .='
<em>zGodX &raquo; <b>Mailboxes</b></em><br><br>
<table class="table table-striped">
            	<tr>
                	<th colspan="1">Account</th>
                    <th colspan="1">Email Address</th>
                    <th colspan="1">Status</th>
                    <th colspan="1">Created</th>
                    <th colspan="1"><center>Actions</center></th>
                </tr>';
     do{
    
	$sql = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['mb_acc_fk']."'";
			$listacc  = $zdbh->query($sql);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch();
      $sql = "SELECT mb_created_ts, mb_enabled_in FROM x_mailboxes WHERE mb_address_vc ='".$row['mb_address_vc']."'";
	      $listhm = $zdbh->query($sql);
				$listhm->setFetchMode(PDO::FETCH_ASSOC);
				$rowahm   = $listhm->fetch();

	$end.='
    			<tr>
    				<td><a href="javascript:void(0)" onClick="document.frmMain.getMain.value=\'user\'; document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\'; document.frmMain.submit();">'.$rowacc['ac_user_vc'].'</a></td>
      				<td>'.$row['mb_address_vc'].'</td>';
                    
		if ($rowahm['mb_enabled_in'] == 1){	
				$status="<font color=\"green\">Enabled</font>";
				}else{
				$status="<font color=\"red\">Disabled</font>";} 
				
      	$end.='			<td>'.$status.'</td>
                    
      				<td>'.date("M d, Y",$row['mb_created_ts']).'</td>
                    <td><center>';
      	if ($rowahm['mb_enabled_in'] == 1){	
          $end.='          					<a title="Disable User Email Account" href="javascript:void(0)"
                    							onClick="document.frmMain.getMain.value=\'mail\'; 	
                                                document.frmMain.getToggleMail.value=\'MAILOFF\';
                                                document.frmMain.getToggleMailID.value=\''.$row['mb_address_vc'].'\';
                                                document.frmMain.submit();"><img src="modules/zgodx/images/locksmall.png" border="0" /></a>';
          }else{ 
                  $end.='        				 <a title="Enable User Email Account" href="javascript:void(0)"
                    							onClick="document.frmMain.getMain.value=\'mail\'; 	
                                                document.frmMain.getToggleMail.value=\'MAILON\';
                                                document.frmMain.getToggleMailID.value=\''.$row['mb_address_vc'].'\';
                                                document.frmMain.submit();"><img src="modules/zgodx/images/unlocksmall.png" border="0" /></a>';
           } 
          $end.='          
          		<a title="Link to Edit User Email Account" href="?module=mailboxes&show=Edit&other='.$row['mb_id_pk'].'"><img src="modules/zgodx/images/editsmall.png" border="0" /></a>
							<a title="Link to Delete User Email Account" href="?module=mailboxes&show=Delete&other='.$row['mb_id_pk'].'"><img src="modules/zgodx/images/deletesmall.png" border="0" /></a>
							</center></td>
    			</tr>';
    } while($row = $list->fetch());
		$end.='	</table>';

} else { 
$end.= "
<em>zGodX &raquo; <b>Email Accounts</b></em><br><br>
<b>You have no mail boxes at this time</b>"; 
} 

return $end;
}
?>