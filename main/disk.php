<?php
#get server hd space
 if (sys_versions::ShowOSName() == "Windows") { #WINDOWS
//define("DISK","c:/");
$dsk="c:/";
}else{ #POSIX
//define("DISK","/");
$dsk="/";
} #ENDIF
define("DISK1",$dsk);


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
}else{ #Not zadmin, we only get resller account info
$sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk='" .$user_x_id. "' OR ac_id_pk='" .$user_x_id. "' AND ac_deleted_ts IS NULL ORDER BY ac_user_vc ASC";
} #Endif
  $listac  = $zdbh->query($sql);
	$listac->setFetchMode(PDO::FETCH_ASSOC);
	$rowac   = $listac->fetch();
  $totalac =$zdbh->query($sql)->rowCount();

$total = disk_total_space(DISK1);
#get server free space
$free = disk_free_space(DISK1);
#get server used space
$used = $total - $free;

//$end.="OS-NAME:".sys_versions::ShowOSName();

if ($totalac > 0 ){

if($total>0) { $mut = round(($used/$total)*100); } else {$mut=0;}
if($mut>0 && $mut<=74) { $dcm = 'g';} elseif($mut>74 && $mut<=90) { $dcm='o'; } elseif($mut>90 && $mut<=100) {$dcm='r';} else { $dcm='b';}
if($total>0) { $mut = $mut.'%'; } else { $mut='U/L';}	


$end.='
<em>zGodX &raquo; <b>Server Disk Usage</b></em><br><br>
<table class="table table-striped">
	<tr>
		<th colspan="2"> Total Server Storage Space </th><th>Used</th><th>Free</th>
    </tr>
    <tr>
        <td><b>'.FormatFileSize($total).'</b></td>
        <td><div id="progress" class="graph"><div id="bar'.$dcm.'" style="width:'.$mut.'"><p>'.$mut.'</p></div></div></td>
        <td><b>'.FormatFileSize($used).'</b></td>
        <td><b>'.FormatFileSize($free).'</b></td>
    </tr>
</table>
<br>
<table class="table table-striped">
            	<tr>
                	<th colspan="1">Account</th>
                    <th colspan="4"><center>Quota Usage</center></th>
                    <th colspan="1">%Server Usage</th>
                    <th colspan="1"><center>Reset</center></th>
                </tr>';
     do{
				if ($rowac['ac_id_pk'] != 1){
				
//			$GQBU = GetQuotaz('bandwidth_usage', $rowac['ac_id_pk']);
//			$GQBM = GetQuotaz('bandwidth_max', $rowac['ac_id_pk']);
//			$GQDU = 
//			$GQDM = 


$pbu = GetQuotaz('diskspace_usage', $rowac['ac_id_pk']);
$pbt = GetQuotaz('diskspace_max', $rowac['ac_package_fk']);
if($pbt>0) { $maxu = round(($pbu/$pbt)*100); } else {$maxu=0;}
if($maxu>0 && $maxu<=74) { $dcu = 'g';} elseif($maxu>74 && $maxu<=90) { $dcu='o'; } elseif($maxu>90 && $maxu<=100) {$dcu='r';} else { $dcu='b';}
if($pbt>0) { $maxu = $maxu.'%'; } else { $maxu='U/L';}


$sbu = GetQuotaz('diskspace_usage', $rowac['ac_id_pk']);
$sbt = $total;
if($sbt>0) { $maxs = round(($sbu/$sbt)*100); } else {$maxs=0;}
if($maxs>0 && $maxs<=74) { $dcs = 'g';} elseif($maxs>74 && $maxs<=90) { $dcs='o'; } elseif($maxs>90 && $maxs<=100) {$dcs='r';} else { $dcs='b';}
if($sbt>0) { $maxs = $maxs.'%'; } else { $maxs='U/L';}	
		
				
	$end .='			
    			<tr>
    				<td><a href="javascript:void(0)"
                    	onClick="document.frmMain.getMain.value=\'user\';
                        document.frmMain.getUser.value=\''.$rowac['ac_id_pk'].'\';
                        document.frmMain.submit();">'.$rowac['ac_user_vc'].'</a></td>
                    <td>'.FormatFileSize(GetQuotaz('diskspace_usage', $rowac['ac_id_pk'])).'</td>
                    <td><b> of </b></td>
					<td>'.FormatFileSize(GetQuotaz('diskspace_max', $rowac['ac_package_fk'])).'</td>
                    <td><div id="progress" class="graph"><div id="bar'.$dcu.'" style="width:'.$maxu.'"><p>'.$maxu.'</p></div></div></td>    
                    <td><div id="progress" class="graph"><div id="bar'.$dcs.'" style="width:'.$maxs.'"><p>'.$maxs.'</p></div></div></td> 
                    <td align="center">';
                    
                     if ($rowac['ac_user_vc'] == "zadmin" || $rowac['ac_id_pk'] != $user_x_id){
                     $end.='
                                        <a title="Reset Disk Quota" href="javascript:void(0)"
                    					onClick="document.frmMain.getMain.value=\'disk\';
                                        document.frmMain.getResetQuota.value=\'RESETDS\';
                                        document.frmMain.getResetQuotaID.value=\''.$rowac['ac_id_pk'].'\';
                                        document.frmMain.submit(); "><center><img src="modules/zgodx/images/reset.png" border="0" /></center></a>';
                 }else{
                  $end.='N/A';
                 }
           $end.='</td>
    			</tr>';
        }
    } while($rowac = $listac->fetch());
$end.='			</table>';

 } else { 
 $end.= "
 <em>zGodX &raquo; <b>Server Disk Usage</b></em><br><br>
 <b>It seems you have no used no disk space at all... Strange...</b>"; 
 } 
 
return $end;
}
?>