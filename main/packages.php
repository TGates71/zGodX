<?php
function zGodX_main_data(){
      $end ='';
			$user_array = ctrl_users::GetUserDetail();
			$user_x  = $user_array['username'];
			$user_x_id = $user_array['userid'];
      global $zdbh;

if (isset($_POST['getDeletePACK']) && $_POST['getDeletePACK'] == "DELETEPACK"){ $end.=zGodxDeletePack($_POST['getDeletePACKID'], $_POST['getDeletePACKNAME']);}

#get packages
if ($user_x == "zadmin" || $user_x_id ==1){ #If zadmin we get everything
$sql = "SELECT * FROM x_packages WHERE pk_deleted_ts IS NULL ORDER BY pk_reseller_fk ASC";
}else{ #Not zadmin, we only get resller account info
$sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk='" .$user_z_id. "' OR ac_id_pk='" .$user_x_id. "' AND ac_deleted_ts IS NULL";
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
$sql = "SELECT * FROM x_packages WHERE pk_reseller_fk IN('".join("','", $client)."')  AND pk_deleted_ts IS NULL ORDER BY pk_reseller_fk ASC";
} #Endif
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();
if ($total > 0){

$end.='
<em>zGodX &raquo; <b>Packages</b></em><br><br>
<table class="table table-striped">
            	<tr>
                	<th colspan="1">Account</th>
                    <th colspan="1">Package Name</th>
                    <th colspan="1">Is Admin</th>
                    <th colspan="1">PHP</th>
					<!-- TG - pk_enablecgi_in does not exist in Sentora
                    <th colspan="1">CGI</th>-->
                    <th colspan="1">Created</th>
                    <th colspan="1"><center>Actions</center></th>
                </tr>';
  do{
    
	$sql = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['pk_reseller_fk']."'";
			$listacc  = $zdbh->query($sql);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch();
			
		if($row['pk_name_vc'] == "LOCKED ACCOUNTS"){
		   $name = "<font color=\"red\">".$row['pk_name_vc']."</font>";
		}else{
		   $name = $row['pk_name_vc'];
		}
		
		if($name == 'Administration'){ # if no changes for group name 1 :)
    $is_admin = 'YES';
    } else{
    $is_admin = 'NO';
    }

    $end.='			<tr>
    				<td><a href="javascript:void(0)" onClick="document.frmMain.getMain.value=\'user\'; document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\'; document.frmMain.submit();">'.$rowacc['ac_user_vc'].'</a></td>
      				<td>'.$name.'</td>
              <td>'.$is_admin.'</td>';
                    
              if ($row['pk_enablephp_in'] == 1){
							$php_x="YES";
							}else{
							$php_x="NO";
							}
                    
               $end.='     <td>'.$php_x.'</td>';
/* TG - pk_enablecgi_in does not exist in Sentora
              if ($row['pk_enablecgi_in'] == 1){
							$cgi_x="YES";
							}else{
							$cgi_x="NO";
							}
                    
               $end.='     <td>'.$cgi_x.'</td>';
*/                            
                $end.='    <td>';

							if (empty($row['pk_created_ts'])){
							$end.= "on install";
							}else{
							$end.= date("M d, Y",$row['pk_created_ts']);}
							$end .='</td>
							<td><center>
							<a title="Link to Edit Package" href="?module=packages&show=Edit&other='.$row['pk_id_pk'].'"><img src="modules/zgodx/images/editsmall.png" border="0" /></a>
							<a title="Link to Delete Package" href="?module=packages&show=Delete&other='.$row['pk_id_pk'].'"><img src="modules/zgodx/images/deletesmall.png" border="0" /></a>							</center></td>
    			</tr>';
 } 
 
  while($row = $list->fetch());
	$end.=		'</table>';

} else { 
$end.= "
<em>zGodX &raquo; <b>Packages</b></em><br><br>
<b>You have no packages at this time</b>"; 
} 

return $end;
}
?>