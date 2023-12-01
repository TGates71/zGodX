<?php
function zGodX_main_data(){
      $end ='';

			$user_array = ctrl_users::GetUserDetail();
			$user_x  = $user_array['username'];
			$user_x_id = $user_array['userid'];
      global $zdbh;

if (isset($_POST['getResetQuota'])){$end.=ResetQuota($_POST['getResetQuota'], $_POST['getResetQuotaID']);}

#get all accounts
if ($user_x == "zadmin" || $user_x_id ==1){ #If zadmin we get everything

$sql = "SELECT * FROM x_accounts WHERE ac_deleted_ts IS NULL ORDER BY ac_user_vc ASC";
			$listac  = $zdbh->query($sql);
			$listac->setFetchMode(PDO::FETCH_ASSOC);
			$rowac   = $listac->fetch();
      $totalac = $zdbh->query($sql)->rowCount();
#get monthly bandwidth
$sql = "SELECT SUM(bd_transamount_bi) AS asum FROM x_bandwidth WHERE bd_month_in ='".date('Ym')."'";
			$listmd  = $zdbh->query($sql);
			$listmd->setFetchMode(PDO::FETCH_ASSOC);
			$rowmd   = $listmd->fetch();
#get yearly bandwidth
$sql = "SELECT SUM(bd_transamount_bi) AS asum FROM x_bandwidth WHERE bd_month_in LIKE '".date('Y')."%'";
			$listyd  = $zdbh->query($sql);
			$listyd->setFetchMode(PDO::FETCH_ASSOC);
			$rowyd   = $listyd->fetch();
#get total bandwidth
$sql = "SELECT SUM(bd_transamount_bi) AS asum FROM x_bandwidth";
			$listtd  = $zdbh->query($sql);
			$listtd->setFetchMode(PDO::FETCH_ASSOC);
			$rowtd   = $listtd->fetch();

}else{ #Not zadmin, we only get resller account info

$sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk='" .$user_x_id . "' OR ac_id_pk='" .$user_x_id . "' AND ac_deleted_ts IS NULL ORDER BY ac_user_vc ASC";
			$listac  = $zdbh->query($sql);
			$listac->setFetchMode(PDO::FETCH_ASSOC);
			$rowac   = $listac->fetch();
      $totalac = $zdbh->query($sql)->rowCount();
#get monthly bandwidth
$sql = "SELECT SUM(bd_transamount_bi) AS asum FROM x_bandwidth WHERE bd_month_in ='".date('Ym')."' AND bd_acc_fk='" .$useraccount['ac_id_pk']. "'";
			$listmd  = $zdbh->query($sql);
			$listmd->setFetchMode(PDO::FETCH_ASSOC);
			$rowmd   = $listmd->fetch();
#get yearly bandwidth
$sql = "SELECT SUM(bd_transamount_bi) AS asum FROM x_bandwidth WHERE bd_month_in LIKE '".date('Y')."%' AND bd_acc_fk='" .$useraccount['ac_id_pk']. "'";
			$listyd  = $zdbh->query($sql);
			$listyd->setFetchMode(PDO::FETCH_ASSOC);
			$rowyd   = $listyd->fetch();
#get total bandwidth
$sql = "SELECT SUM(bd_transamount_bi) AS asum FROM x_bandwidth";
			$listtd  = $zdbh->query($sql);
			$listtd->setFetchMode(PDO::FETCH_ASSOC);
			$rowtd   = $listtd->fetch();

} #Endif

if ($totalac > 0 ){
$end.='
<em>zGodX &raquo; <b>Server Bandwidth</b></em><br><br>
<table class="table table-striped">
	<tr>
		<th><center>Server Bandwidth for '.date('M').'</center></th><th><center>Server Bandwidth for '.date('Y').'</center></th><th><center>Total Server Bandwidth</center></th>
    </tr>
    <tr>
        <td><center>'.FormatFileSize($rowmd['asum']).'</center></td>
        <td><center>'.FormatFileSize($rowyd['asum']).'</center></td>
        <td><center>'.FormatFileSize($rowtd['asum']).'</center></td>
    </tr>
</table>
<br>
<table class="table table-striped">
            	<tr>
                	<th colspan="1">Account</th>
                    <th colspan="4"><center>Quota Usage</center></th>
                    <th colspan="1">%Server Usage</th>
                    <th colspan="1"><center>Actions</center></th>
                </tr>';
     do{
				if ($rowac['ac_id_pk'] != 1){
    
	//$sql = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$rowbw['bd_acc_fk']."'"; ??? rowbw does not exist...
	$sql = "SELECT ac_user_vc, ac_id_pk FROM x_accounts";
			$listacc  = $zdbh->query($sql);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch();
			
			
			//$GT_QTB = GetQuotaz('bandwidth_usage', $rowac['ac_id_pk']);
			//$GT_QMD = GetQuotaz('diskspace_max', $rowac['ac_package_fk']);

$pbu = GetQuotaz('bandwidth_usage', $rowac['ac_id_pk']);
$pbt = GetQuotaz('diskspace_max', $rowac['ac_package_fk']);
if($pbt>0) { $maxu = round(($pbu/$pbt)*100); } else {$maxu=0;}
if($maxu>0 && $maxu<=74) { $dcu = 'g';} elseif($maxu>74 && $maxu<=90) { $dcu='o'; } elseif($maxu>90 && $maxu<=100) {$dcu='r';} else { $dcu='b';}
if($pbt>0) { $maxu = $maxu.'%'; } else { $maxu='U/L';}


$sbu = GetQuotaz('bandwidth_usage', $rowac['ac_id_pk']);
$sbt = $rowmd['asum'];
if($sbt>0) { $maxs = round(($sbu/$sbt)*100); } else {$maxs=0;}
if($maxs>0 && $maxs<=74) { $dcs = 'g';} elseif($maxs>74 && $maxs<=90) { $dcs='o'; } elseif($maxs>90 && $maxs<=100) {$dcs='r';} else { $dcs='b';}
if($sbt>0) { $maxs = $maxs.'%'; } else { $maxs='U/L';}	

			
			
    	$end.='		<tr>
    				<td><a href="javascript:void(0)" onClick="document.frmMain.getMain.value=\'user\'; document.frmMain.getUser.value=\''.$rowac['ac_id_pk'].'\'; document.frmMain.submit();">'.$rowac['ac_user_vc'].'</a></td>
                    <td>'.FormatFileSize((GetQuotaz('bandwidth_usage', $rowac['ac_id_pk']))).'</td>
                    <td><b>of</b></td>
					<td>'.FormatFileSize((GetQuotaz('bandwidth_max', $rowac['ac_package_fk']))).'</td>';
					   $end.='<td><div id="progress" class="graph"><div id="bar'.$dcu.'" style="width:'.$maxu.'"><p>'.$maxu.'</p></div></div></td>    
                    <td><div id="progress" class="graph"><div id="bar'.$dcs.'" style="width:'.$maxs.'"><p>'.$maxs.'</p></div></div></td> 
                         

                    <td align="center">';
          if ($rowac['ac_user_vc'] == "zadmin" || $rowac['ac_id_pk'] != $user_x_id){
					$end.='<a href="javascript:void(0)" onClick="document.frmMain.getMain.value=\'bw\'; document.frmMain.getResetQuota.value=\'RESETBW\'; document.frmMain.getResetQuotaID.value=\''.$rowac['ac_id_pk'].'\'; document.frmMain.submit();"><center><img title="Reset BandthWidth Quota" src="modules/zgodx/images/reset.png" border="0"/></center></a>
				';
			 }else{
          $end.='N/A';
        }
      $end.='  </td>
    			</tr>';
	}
			} while($rowac = $listac->fetch());

$end.='			</table>';

} else { 
$end.="<em>zGodX &raquo; <b>Server Bandwidth</b></em><br><br>
<b>It seems you have no used bandwidth at all... Internet problems?...</b>"; 
} 

return $end;
}
?>