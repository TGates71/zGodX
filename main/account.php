<?php
function zGodX_main_data(){
$end='';
			$user_array = ctrl_users::GetUserDetail();
			$user_x  = $user_array['username'];
			$user_x_id = $user_array['userid'];
			global $zdbh;
			
if (isset($_POST['getLockAcc'])){ $end.=ToggleAcc($_POST['getLockAcc'], $_POST['getLockAccID'], $_POST['getLockAccReason']);}
if (isset($_POST['getShAcc'])){ $end.=xShadowUser($_POST['getShAccID']);}

#get accounts
if ($user_x == "zadmin" || $user_x_id ==1){ #If zadmin we get everything
$sql = "SELECT * FROM x_accounts WHERE ac_deleted_ts IS NULL ORDER BY ac_user_vc ASC";
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
$sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk IN('".join("','", $client)."') OR ac_id_pk='".$user_x_id."' AND ac_deleted_ts IS NULL ORDER BY ac_user_vc ASC";
} #Endif
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();
if ($total > 0 ){
	if((isset($_GET['r'])) && ($_GET['r']=='user')){
		include ('modules/zgodx/main/user.php');
		}else{

$end.='
<em>zGodX &raquo; <b>User Account Summary</b></em><br><br>';
/*
<table border="0" cellpadding="0" cellspacing="0" width="">
	<tr>
		<td align="left">
*/
$end.='        	<table class="table table-striped">
            	<tr>
                	<th colspan="1">Account</th>
                    <th colspan="1">Package</th>
                    <th colspan="1">Reseller</th>
                    <th colspan="1">BW</th>
                    <th colspan="1">DU</th>
                    <th colspan="1"><center>Status</center></th>
                    <th colspan="1">Created</th>                    
                    <th colspan="1"><center>Actions</center></th>
                </tr>';
 do{
    			if ($row['ac_id_pk'] != 1){ 
$end.='    			<tr>
                    <td><a href="javascript:void(0)"
                        onClick="document.frmMain.getMain.value=\'user\';
                        document.frmMain.getUser.value=\''.$row['ac_id_pk'].'\';
                        document.frmMain.submit();">'.$row['ac_user_vc'].'</a>:</td>';
                    

				$sql = "SELECT pk_name_vc FROM x_packages WHERE pk_id_pk = '".$row['ac_package_fk']."'";
			$listp  = $zdbh->query($sql);
			$listp->setFetchMode(PDO::FETCH_ASSOC);
			$rowp   = $listp->fetch();
		if($rowp['pk_name_vc'] == "LOCKED ACCOUNTS"){
		   $pname = "<font color=\"red\">".$rowp['pk_name_vc']."</font>";
		}else{
		   $pname = $rowp['pk_name_vc'];
		} 
           $end.='         <td><small>'.$pname.'</small></td>  ';
                    
   
				$sql = "SELECT ac_user_vc, ac_pass_vc FROM x_accounts WHERE ac_reseller_fk = '".$row['ac_reseller_fk']."'";
			$listres  = $zdbh->query($sql);
			$listres->setFetchMode(PDO::FETCH_ASSOC);
			$rowres   = $listres->fetch();
			
$pbu = GetQuotaz('bandwidth_usage', $row['ac_id_pk']);
$pbt = GetQuotaz('bandwidth_max', $row['ac_package_fk']);
if($pbt>0) { $maxu = round(($pbu/$pbt)*100); } else {$maxu=0;}
if($maxu>0 && $maxu<=74) { $dcu = 'g';} elseif($maxu>74 && $maxu<=90) { $dcu='o'; } elseif($maxu>90 && $maxu<=100) {$dcu='r';} else { $dcu='b';}
if($pbt>0) { $maxu = $maxu.'%'; } else { $maxu='U/L';}


$sbu = GetQuotaz('diskspace_usage', $row['ac_id_pk']);
$sbt = GetQuotaz('diskspace_max', $row['ac_package_fk']);
if($sbt>0) { $maxs = round(($sbu/$sbt)*100); } else {$maxs=0;}
if($maxs>0 && $maxs<=74) { $dcs = 'g';} elseif($maxs>74 && $maxs<=90) { $dcs='o'; } elseif($maxs>90 && $maxs<=100) {$dcs='r';} else { $dcs='b';}
if($sbt>0) { $maxs = $maxs.'%'; } else { $maxs='U/L';}


                    $end.=' <td>'.$rowres['ac_user_vc'].'</td>';
             
          $end.='        <td><div id="progress" class="graph"><div id="bar'.$dcu.'" style="width:'.$maxu.'"><p>'.$maxu.'</p></div></div></td>
                         <td><div id="progress" class="graph"><div id="bar'.$dcs.'" style="width:'.$maxs.'"><p>'.$maxs.'</p></div></div></td>';
             
                    
              if(substr($row['ac_pass_vc'], 0, 8) == '!!!!!!!!'){
						$status="<font color=\"red\">LOCKED</font>";
						}else{
						$status="<font color=\"green\">OK</font>";} 

              $end.='      <td><center>'.$status.'</center></td>';
		        
		       if ($row['ac_user_vc'] == "zadmin" || $row['ac_id_pk'] == $user_x_id){ 
               $end.='     						<td></td><td></td>';
               
				   }else{
				                  $end.='		<td>'.date("M d, Y",$row['ac_created_ts']).'</td>'; 
				                  
				                  if(substr($row['ac_pass_vc'], 0, 8) != '!!!!!!!!'){
                    					$end.='	<td><center>
                                          <a title="Lock User Account" href="javascript:void(0)"
                                       onClick="document.frmMain.getMain.value=\'account\'; 	
                                                document.frmMain.getLockAcc.value=\'LOCKACC\';
                                                document.frmMain.getLockAccID.value=\''.$row['ac_id_pk'].'\';
                                                document.frmMain.getLockAccReason.value=\'\';
                                                document.frmMain.submit();"><img src="modules/zgodx/images/locksmall.png" border="0" /></a>';
                                          } else {      
                                              
                                   $end.=' <td><center>
                                   <a title="UnLock User Account" href="javascript:void(0)"
                                       onClick="document.frmMain.getMain.value=\'account\'; 	
                                                document.frmMain.getLockAcc.value=\'UNLOCKACC\';
                                                document.frmMain.getLockAccID.value=\''.$row['ac_id_pk'].'\';
                                                document.frmMain.getLockAccReason.value=\'\';
                                                document.frmMain.submit();"><img src="modules/zgodx/images/unlocksmall.png" border="0" /></a>';
                                          }          
              $end.='
              <a title="Link to Edit User Account" href="?module=manage_clients&show=Edit&other='.$row['ac_id_pk'].'"><img src="modules/zgodx/images/editsmall.png" border="0" /></a>
							<a title="Link to Delete User Account" href="?module=manage_clients&show=Delete&other='.$row['ac_id_pk'].'"><img src="modules/zgodx/images/deletesmall.png" border="0" /></a>
                                                
              
              
              <a title="Shadow user" href="javascript:void(0)"
                                       onClick="document.frmMain.getMain.value=\'account\'; 	
                                                document.frmMain.getShAcc.value=\'SHADOWIT\';
                                                document.frmMain.getShAccID.value=\''.$row['ac_id_pk'].'\';
                                                document.frmMain.submit();"><img src="modules/zgodx/images/shadow.png" border="0" /></a>
              
                                                
                                                </center></td>';

				 }
                    
      		
          $end.='       </tr>';
            }
			} 
			while($row = $list->fetch());
			
			$end.='</table>';
/*			
        </td>
      </tr>
  </table>';
*/
      }

  } else { 

  $end.= "
  <em>zGodX &raquo; <b>User Account Summary</b></em><br><br>
  <b>You have no Reseller accounts at this time... I don't think you should ever see this.</b>"; 

  }

return $end;
}
?>