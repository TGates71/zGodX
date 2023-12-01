<?php
function zGodX_main_data(){
      $end ='';

			$user_array = ctrl_users::GetUserDetail();
			$user_x  = $user_array['username'];
			$user_x_id = $user_array['userid'];
      global $zdbh;

//if (isset($_POST['getDeleteVHOST']) && $_POST['getDeleteVHOST'] == "DELETEVHOST"){ $end.=zGodxDeleteVhost($_POST['getDeleteVHOSTID'], $_POST['getDeleteVHOSTNAME']);}
      
#get vhosts
if ($user_x == "zadmin" || $user_x_id ==1){ #If zadmin we get everything
$sql = "SELECT * FROM x_vhosts WHERE vh_deleted_ts IS NULL ORDER BY vh_acc_fk ASC";
}else{ #Not zadmin, we only get resller account info
$sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk='" .$user_x_id. "' OR ac_id_pk='" .$user_x_id. "' AND ac_deleted_ts IS NULL";
$total = $zdbh->query($sql)->rowCount();
				$list = $zdbh->query($sql);
				$list->setFetchMode(PDO::FETCH_ASSOC);
				
$client=array();
 if($total <> 0){
	while($row = $list->fetch()){
	$client[] = $row['ac_id_pk'];
    }
 }
$sql = "SELECT * FROM x_vhosts WHERE vh_acc_fk IN('".join("','", $client)."') AND vh_deleted_ts IS NULL";
} #Endif
				$list = $zdbh->query($sql);
				$list->setFetchMode(PDO::FETCH_ASSOC);
				$row   = $list->fetch();
$total = $zdbh->query($sql)->rowCount();
if ($total > 0){

$end.='
<em>zGodX &raquo; <b>Virtual Hosts</b></em><br><br>
        	<table class="table table-striped">
            	<tr>
                	<th colspan="1">Account</th>
                    <th colspan="1">Host</th>
                    <th colspan="1">Directory</th>
                    <th colspan="1"><center>Type</center></th>
                    <th colspan="1">Status</th>
                    <th colspan="1">Created</th>
                    <th colspan="1"><center>Actions</center></th>
                </tr>';
                
    do{
    
	$sql = "SELECT * FROM x_accounts WHERE ac_id_pk ='" .$row['vh_acc_fk']."'";
        $listacc = $zdbh->query($sql);
				$listacc->setFetchMode(PDO::FETCH_ASSOC);
				$rowacc   = $listacc->fetch();
        $quband  = GetQuotaz('bandwidth_usage', $rowacc['ac_id_pk']);
        $qmband  = GetQuotaz('bandwidth_max', $rowacc['ac_package_fk']);
        $LNK = "<a href=\"http://".$row['vh_name_vc']."\" target=\"_blank\">".$row['vh_name_vc'];
	
	$end.='
     			<tr>
    				<td><a href="javascript:void(0)" onClick="document.frmMain.getMain.value=\'user\'; document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\'; document.frmMain.submit();">'.$rowacc['ac_user_vc'].'</a></td>
      				<td>'.$LNK.'</td>
                    <td>'.$row['vh_directory_vc'].'</td>';
                    
           	if ($row['vh_type_in'] == 1){
							$type='<center><img title="Domain" src="modules/zgodx/images/domain.png"></center>';
							}
              elseif ($row['vh_type_in'] == 2){	
							$type='<center><img title="Subdomain" src="modules/zgodx/images/subdomain.png"></center>';
							}else{
							$type='<center><img title="Parked domain" src="modules/zgodx/images/parked.png"></center>';
							} 
                    
             $end.='       <td>'.$type.'</td>';
                    
						if ($quband > $qmband && $qmband!="0"){    // $qmband=="0" unlimited
								$status="<font color=\"red\">LOCKED</font>";
							}else{
								if ($row['vh_active_in'] == 1){
								$status="<font color=\"green\">Live</font>";
								}else{
								$status="<font color=\"orange\">Pending</font>";
								} 
							}
                            
                $end.=    '<td>'.$status.'</td>
      				<td>'.date("M d, Y",$row['vh_created_ts']).'</td>
      				<td><a title="Link to delete Virtual Host" href="?module=domains&show=Delete&id='.$row['vh_id_pk'].'&domain='.$row['vh_name_vc'].'">
      				<center><img src="modules/zgodx/images/deletesmall.png" border="0" /></center></a></td>
    			</tr>';
     } while($row = $list->fetch());
     
	$end.='		</table>';

} else { 
$end.= "
<em>zGodX &raquo; <b>Virtual Host Domains</b></em><br><br>
<b>You have no domains at this time</b>"; 
}

return $end;
}
?>