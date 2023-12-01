<?php
function zGodX_main_data(){
      $end ='';
			$user_array = ctrl_users::GetUserDetail();
			$user_x  = $user_array['username'];
			$user_x_id = $user_array['userid'];
      global $zdbh;

#get databases
if ($user_x == "zadmin" || $user_x_id ==1){ #If zadmin we get everything
$sql = "SELECT * FROM x_mysql_databases WHERE my_deleted_ts IS NULL ORDER BY my_acc_fk ASC";
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
$sql = "SELECT * FROM x_mysql_databases WHERE my_acc_fk IN('".join("','", $client)."') AND my_deleted_ts IS NULL ORDER BY my_acc_fk ASC";
} #Endif
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();
if ($total > 0){

$end.='
<em>zGodX &raquo; <b>MySQL Databases</b></em> <a href="apps/phpmyadmin/" target="_blank">(Launch phpMyAdmin)</a><br><br>
<table class="table table-striped">
            	<tr>
                	<th colspan="1">Account</th>
                    <th colspan="1">Database</th>
                    <th colspan="2">Disk Usage</th>
                    <th colspan="1">Created</th>
                    <th colspan="1"><center>Actions</center></th>
                </tr>';
     do{
    
	$sql = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['my_acc_fk']."'";
			$listacc  = $zdbh->query($sql);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch();
	$space   = FormatFileSize($row['my_usedspace_bi']);
	$space   = explode(" ", $space);
$end.='
    			<tr>
    				<td><a href="javascript:void(0)" onClick="document.frmMain.getMain.value=\'user\'; document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\'; document.frmMain.submit();">'.$rowacc['ac_user_vc'].'</a></td>
                    <td>'.$row['my_name_vc'].'</td>    
                    <td>'.$space[0].'</td>
                    <td><b>'.$space[1].'</b></td>
      				<td>'.date("M d, Y",$row['my_created_ts']).'</td>
      				<td><center><a title="Link to Delete Database" href="?module=mysql_databases&show=Delete&other='.$row['my_id_pk'].'"><img src="modules/zgodx/images/deletesmall.png" border="0" /></a></center></td>
    			</tr>';
    			
    } while($row = $list->fetch());
    
	$end.='		</table>';

 } else { 
 $end.= "
 <em>zGodX &raquo; <b>MySQL Databases</b></em><br /><br />
 <b>You have no databases at this time</b>"; 
 }
 
 return $end;
} 
?>