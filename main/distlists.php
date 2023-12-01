<?php
function zGodX_main_data(){
      $end ='';

			$user_array = ctrl_users::GetUserDetail();
			$user_x  = $user_array['username'];
			$user_x_id = $user_array['userid'];
      global $zdbh;
      
#get distrobution lists
if ($user_x == "zadmin" || $user_x_id ==1){ #If zadmin we get everything
$sql = "SELECT * FROM x_distlists WHERE dl_deleted_ts IS NULL ORDER BY dl_acc_fk ASC";
}else{ #Not zadmin, we only get resller account info
$sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk='" .$user_x_id. "' OR ac_id_pk='" .$user_x_id. "' AND ac_deleted_ts IS NULL";
$list = $zdbh->query($sql)->execute();
$total = $zdbh->query($sql)->rowCount();
$client=array();
 if($total <> 0){
	while($row = $list->fetch()){
	$client[] = $row['ac_id_pk'];
    }
 }
$sql = "SELECT * FROM x_distlists WHERE dl_acc_fk IN('".join("','", $client)."') AND dl_deleted_ts IS NULL";
} #Endif

			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();


if ($total > 0){

$end.='
<em>zGodX &raquo; <b>Distribution Lists</b></em><br><br>
<table class="table table-striped">
            	<tr>
                	<th colspan="1">Account</th>
                    <th colspan="1">Address</th>
                    <th colspan="1">Created</th>
                    <th colspan="1"><center>Actions</center></th>
                </tr>';
   do{
    
	$sql = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['dl_acc_fk']."'";
			$listacc  = $zdbh->query($sql);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch();
			
    $end.='			<tr>
    				<td><a href="javascript:void(0)" 
                    		onClick="document.frmMain.getMain.value=\'user\';
                    		document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\';
                            document.frmMain.submit();">'.$rowacc['ac_user_vc'].'</a></td>
      				<td>'.$row['dl_address_vc'].'</td>                            
                    <td>'.date("M d, Y",$row['dl_created_ts']).'</td>
                    <td><center><a href="#link_'.$row['dl_acc_fk'].'" title="Show Distribution List" onclick="toggle_visibility(\'ex_'.$row['dl_acc_fk'].'\');"><img src="modules/zgodx/images/show.png" border="0" /></a>
                    <a title="Link to Delete Distribution List" href="?module=distlists&show=Delete&other='.$row['dl_id_pk'].'"><img src="modules/zgodx/images/deletesmall.png" border="0" /></a></center></td>
    			</tr>
                <tr>
                	<td colspan="4">';
              $sqldlu = "SELECT * FROM x_distlistusers WHERE du_distlist_fk ='" .$row['dl_id_pk']."'";
              $listdlu  = $zdbh->query($sqldlu);
              $listdlu->setFetchMode(PDO::FETCH_ASSOC);
              $rowdlu   = $listdlu->fetch();
              
              $end.='  	<div id="ex_'.$row['dl_acc_fk'].'" style="display:none;">';
					 do{ 
							$end.= $rowdlu['du_address_vc'].'<br>';
							 } while($rowdlu = $listdlu->fetch());
							
                 		
$end.='		   			<br /><br /><br />
                    </div>
                    </td>
           		</tr>';
     } while($row = $list->fetch());
	$end.='		</table>

<script type="text/javascript">
<!--
    function toggle_visibility(id) {
       var e = document.getElementById(id);
       if(e.style.display == \'none\')
          e.style.display = \'block\';
       else
          e.style.display = \'none\';
    }
//-->
</script>';

} else {
$end.= "
<em>zGodX &raquo; <b>Distribution Lists</b></em><br><br>
<b>You have no distrubution lists at this time</b>";
}

return $end;
}
?>