<?php 

#get server hd space
 if (sys_versions::ShowOSName() == "Windows") { #WINDOWS
define("DISK1","c:/");
}else{ #POSIX
define("DISK1","/");
} #ENDIF

function zGodX_main_data(){
      $end ='';

			$user_array = ctrl_users::GetUserDetail();
			$user_x  = $user_array['username'];
			$user_x_id = $user_array['userid'];
      global $zdbh;

	if (isset($_POST['getLockAcc'])){ $end.= ToggleAcc($_POST['getLockAcc'], $_POST['getLockAccID'], $_POST['getLockAccReason']);	}
	if (isset($_POST['getResetQuota'])){$end.=ResetQuota($_POST['getResetQuota'], $_POST['getResetQuotaID']);}
	if (isset($_POST['getDeleteFTP'])){$end.=zGodXDeleteFTP($_POST['getDeleteFTP'], $_POST['getDeleteFTPUSER'], $_POST['getDeleteFTPID'], $_POST['getDeleteFTPACC']);}
	if (isset($_POST['getToggleMail'])){ $end.=zToggleMail($_POST['getToggleMail'], $_POST['getToggleMailID']);} //pop up ???
	if (isset($_POST['getDeleteCRON']) && $_POST['getDeleteCRON'] == "DELETECRON"){$end.=zGodxDeleteCron($_POST['getDeleteCRONUSERID'], $_POST['getDeleteCRONID']);}
	if (isset($_POST['getUserEdit'])){ $end.= UserEdit($_POST['getEditAcc'], $_POST['getFullName'], $_POST['getEmail'], $_POST['getAddress'], $_POST['getPost'], $_POST['getPhone'], $_POST['getUser']); }
	 

$userid = $_POST['getUser'];


#get user information
$sql 	 = "SELECT * FROM x_accounts WHERE ac_id_pk ='".$userid."'";
	      $listac = $zdbh->query($sql);
				$listac->setFetchMode(PDO::FETCH_ASSOC);
				$rowac   = $listac->fetch();
        $totalac =$zdbh->query($sql)->rowCount();
#get personal information
$sql 	 = "SELECT * FROM x_profiles WHERE ud_user_fk ='".$userid."'";
	      $listp = $zdbh->query($sql);
				$listp->setFetchMode(PDO::FETCH_ASSOC);
				$rowp   = $listp->fetch();
				$totalp =$zdbh->query($sql)->rowCount();


$end.='<em>zGodX &raquo; <b>Information for user: '.$rowac['ac_user_vc'].'</b></em>
<br /><br />
            <table class="table table-striped">
             	<tr>
                	<th colspan="4">User activity on the server for the last 30 days <span style="float:right"><a href="#_link" title="Show this months activity" onclick="toggle_visibility(\'logs_lastmonth\');"><img src="modules/zgodx/images/show.png" border="0" /></a></span></th>
                </tr>
            </table>
            <div id="logs_lastmonth" style="display:none;">
            <table class="table table-striped">';

			$lastmonth = time() - (30 * 24 * 60 * 60);
			$lastmonth = date("Y-m-d H:i:s", $lastmonth); 
			$lastweek  = time() - (7 * 24 * 60 * 60);
			$lastweek = date("Y-m-d H:i:s", $lastweek); 
			$sql 	   = "SELECT * FROM x_logs WHERE lg_user_fk ='".$userid."' AND lg_when_ts >= '".$lastmonth ."' ORDER BY lg_when_ts DESC";
	      $list = $zdbh->query($sql);
				$list->setFetchMode(PDO::FETCH_ASSOC);
				$row   = $list->fetch();
				$total =$zdbh->query($sql)->rowCount();

      $date = $row['lg_when_ts'] ?? '';
      
    if ($total <> 0){
      
			do{
			
			$end.='
            	<tr>
                	<td>'.$date.'</td>
                    <td><a href="javascript:void(0)"
                        onClick="document.frmMain.getMain.value=\'user\';
                        document.frmMain.getUser.value=\''.$rowac['ac_id_pk'].'\';
                        document.frmMain.submit();">'.$rowac['ac_user_vc'].'</a>:</td>
					<td>'.$row['lg_detail_tx'].'</td>
                </tr>';
                
      } 
      while($row = $list->fetch());
     
   } else {
   $end.='
            	<tr>
                	<td>No records found</td>
              </tr>';  	
   }          
     
      
      $end.='
                
            </table> 
            </div>      
<br />
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr valign="top">
		<td align="left">
			<table class="table table-striped">
            	<tr>
                	<th>User Information</th><th><span style="float:right">';
                	
            if ($rowac['ac_user_vc'] == "zadmin" || $rowac['ac_id_pk'] == 1){ 
            } else{
            $end .= '<a href="#link_editp" title="Edit User Information" onclick="toggle_visibility(\'editp\');"><img src="modules/zgodx/images/editsmall.png" border="0" /></span></th>';
            }    	
                	
                	
        $end.='</tr>
				<tr>
					<td colspan="2">
					<div id="editp" style="display:none;">
					<FORM name="frmMainx" id="frmMainx" action="" method="POST">
					<INPUT type="hidden" name="getEditAcc" value="EDIT"> 
					<INPUT type="hidden" name="getMain" value="user"> 
					<INPUT type="hidden" name="getUser" value="'.$rowac['ac_id_pk'].'">
					<INPUT type="hidden" name="getUserEdit" value="getUserEdit"> 
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr valign="top">
							<td align="left">Full Name:</td><td><input type="text" name="getFullName" value="'.$rowp['ud_fullname_vc'].'" /></td>
						</tr>
						<tr>
							<td align="left">Email:</td><td><input type="text" name="getEmail" value="'.$rowac['ac_email_vc'].'" /></td>
						</tr>
						<tr>	
							<td align="left">FAddress:</td><td><textarea name="getAddress" rows="5">'.$rowp['ud_address_tx'].'</textarea></td>
						</tr>
						<tr>	
							<td align="left">Post code:</td><td><input type="text" name="getPost" value="'.$rowp['ud_postcode_vc'].'" /></td>
						</tr>
						<tr>	
							<td align="left">Phone #:</td><td><input type="text" name="getPhone" value="'.$rowp['ud_phone_vc'].'" /></td>
						</tr>						
						<tr>	
							<td align="left"></td><td><button class="btn" id="button" name="submit" id="submit" value="butt">Save</button></td>
						</tr>
					</table>
					</FORM>
					</div>
					</td>
				</tr>	
            	<tr>
                	<td><b>Account ID:</b></td><td>'.$rowac['ac_id_pk'].'</td>
                </tr>';
             if(substr($rowac['ac_pass_vc'], 0, 8) == '!!!!!!!!'){
						$status="<font color=\"red\">LOCKED</font>";
						}else{
						$status="<font color=\"green\">OK</font>";} 
			$end.='			
				<tr>
					<td><b>Status</b></td>
					      						<td>'.$status.' 
                                            	<span style="float:right">';
								 if ($rowac['ac_user_vc'] == "zadmin" || $rowac['ac_id_pk'] == $user_x_id){ 
                    						
								 }else{ 
								 
								 
                    					$end.='<a title="Lock User Account" href="javascript:void(0)"
                                       onClick="document.frmMain.getMain.value=\'user\'; 	
                                                document.frmMain.getUser.value=\''.$rowac['ac_id_pk'].'\';
                                                document.frmMain.getLockAcc.value=\'LOCKACC\';
                                                document.frmMain.getLockAccID.value=\''.$rowac['ac_id_pk'].'\';
                                                document.frmMain.getLockAccReason.value=\'\';
                                                document.frmMain.submit();"><img src="modules/zgodx/images/locksmall.png" border="0" /></a> 
                                                
                                       <a title="UnLock User Account" href="javascript:void(0)"
                                       onClick="document.frmMain.getMain.value=\'user\'; 	
                                                document.frmMain.getUser.value=\''.$rowac['ac_id_pk'].'\';
                                                document.frmMain.getLockAcc.value=\'UNLOCKACC\';
                                                document.frmMain.getLockAccID.value=\''.$rowac['ac_id_pk'].'\';
                                                document.frmMain.getLockAccReason.value=\'\';
                                                document.frmMain.submit();"><img src="modules/zgodx/images/unlocksmall.png" border="0" /></a>';


                  }
                  
                  
               $addrix = str_replace(',', '<br>', $rowp['ud_address_tx']);   
          $end.='        
												</span>
                                             	</td> 
				</tr>'; 
				if ($rowac['ac_user_vc'] == "zadmin" || $rowac['ac_id_pk'] == 1){ 
				} else{
				        
         $end.='<tr>
                	<td><b>Full Name:</b></td><td>'.$rowp['ud_fullname_vc'].'</td>
                </tr>
             	<tr>
                	<td><b>Email:</b></td><td>'.$rowac['ac_email_vc'].'</td>
                </tr>
             	<tr>
                	<td><b>Address:</b></td><td>'.$addrix.'<br />'.$rowp['ud_postcode_vc'].'</td>
                </tr>
             	<tr>
                	<td><b>Phone Number:</b></td><td>'.$rowp['ud_phone_vc'].'</td>
                </tr>';
         }       
                
       $end.='</table>
			 <br />';
           
#get total bandwidth
$sql    = "SELECT SUM(bd_transamount_bi) AS asum FROM x_bandwidth WHERE bd_month_in ='".date('Ym')."'";
			$listmb  = $zdbh->query($sql);
			$listmb->setFetchMode(PDO::FETCH_ASSOC);
			$rowmb   = $listmb->fetch();


$dtotal = disk_total_space(DISK1);

			$GQBU = GetQuotaz('bandwidth_usage', $userid);
			$GQDU = GetQuotaz('diskspace_usage', $userid);
			$GQBM = GetQuotaz('bandwidth_max', $rowac['ac_package_fk']);
			$GQDM = GetQuotaz('diskspace_max', $rowac['ac_package_fk']);


$pbu = GetQuotaz('diskspace_usage', $userid);
$pbt = GetQuotaz('diskspace_max', $rowac['ac_package_fk']);
if($pbt>0) { $maxu = round(($pbu/$pbt)*100); } else {$maxu=0;}
if($maxu>0 && $maxu<=74) { $dcu = 'g';} elseif($maxu>74 && $maxu<=90) { $dcu='o'; } elseif($maxu>90 && $maxu<=100) {$dcu='r';} else { $dcu='b';}
if($pbt>0) { $maxu = $maxu.'%'; } else { $maxu='U/L';}


$sbu = GetQuotaz('bandwidth_usage', $userid);
$sbt = GetQuotaz('bandwidth_max', $rowac['ac_package_fk']);
if($sbt>0) { $maxs = round(($sbu/$sbt)*100); } else {$maxs=0;}
if($maxs>0 && $maxs<=74) { $dcs = 'g';} elseif($maxs>74 && $maxs<=90) { $dcs='o'; } elseif($maxs>90 && $maxs<=100) {$dcs='r';} else { $dcs='b';}
if($sbt>0) { $maxs = $maxs.'%'; } else { $maxs='U/L';}




$end.='
<b>Bandwidth and Disk Usage</b><br>
			<table class="table table-striped">
				<tr>
                	<th colspan="2" width="50%">Bandwidth Quota</th><th>Reset</th><th colspan="2" width="50%">Disk Quota</th><th>Actions</th>
                </tr>
				<tr>
                	<td>'.FormatFileSize($GQBU).'<b> of </b>'.FormatFileSize($GQBM).'</td>
                    <td><div id="progress" class="graph"><div id="bar'.$dcs.'" style="width:'.$maxs.'"><p>'.$maxs.'</p></div></div></td>
                    <td align="center">';
                   
                    if ($rowac['ac_user_vc'] == "zadmin" || $rowac['ac_id_pk'] != $user_x_id){ 
                    
         $end.='           
                    <a  title="Reset Bandwidth Quota" href="javascript:void(0)"
                    onClick="document.frmMain.getMain.value=\'user\';
                    document.frmMain.getResetQuota.value=\'RESETBW\';
                    document.frmMain.getResetQuotaID.value=\''.$userid.'\';
                    document.frmMain.getUser.value=\''.$rowac['ac_id_pk'].'\';
                    document.frmMain.submit();
                    "><center><img src="modules/zgodx/images/reset.png" border="0"/></center></a>';
                    }else{
                    $end.='N/A';
                    }
                     
                   $end.='</td>
                    
                    
                    <td>'.FormatFileSize($GQDU).'<b> of </b>'.FormatFileSize($GQDM).'</td>
                    <td><div id="progress" class="graph"><div id="bar'.$dcu.'" style="width:'.$maxu.'"><p>'.$maxu.'</p></div></div></td>
                    <td align="center">';
                    
                    if ($rowac['ac_user_vc'] == "zadmin" || $rowac['ac_id_pk'] != $user_x_id){ 
                    
                    $end .='<a title="Reset Disk Quota" href="javascript:void(0)"
                    onClick="document.frmMain.getMain.value=\'user\';
                    document.frmMain.getResetQuota.value=\'RESETDS\';
                    document.frmMain.getResetQuotaID.value=\''.$userid.'\';
                    document.frmMain.getUser.value=\''.$rowac['ac_id_pk'].'\';
                    document.frmMain.submit();
                    "><center><img src="modules/zgodx/images/reset.png" border="0" /></center></a>';
                  }else{
                  $end .='N/A';
                  }
                  
$pdua = $GQBU;
$pdta = $rowmb['asum'];
if($pdta>0) { $maxdua = round(($pdua/$pdta)*100);} else {$maxdua =0;}
if($maxdua>0 && $maxdua<=74) { $dcdua = 'g';} elseif($maxdua>74 && $maxdua<=90) { $dcdua='o'; } elseif($maxdua>90 && $maxdua<=100) {$dcdua='r';} else { $dcdua='b';}
if($pdta>0) { $maxdua = $maxdua.'%'; } else { $maxdua='U/L';}

$sdua = $GQDU;
$sdta = $dtotal;
if($sdta>0) { $maxdsa = round(($sdua/$sdta)*100); } else {$maxdsa =0;}
if($maxdsa>0 && $maxdsa<=74) { $dcdsa = 'g';} elseif($maxdsa>74 && $maxdsa<=90) { $dcdsa='o'; } elseif($maxdsa>90 && $maxdsa<=100) {$dcdsa='r';} else { $dcdsa='b';}
if($sdta>0) { $maxdsa = $maxdsa.'%'; } else { $maxdsa='U/L';}                  
                   
                $end.='
                </td>
                </tr>
                <tr>
                	<td>% of server usage</td>
                    <td colspan="2"><div id="progress" class="graph"><div id="bar'.$dcdua.'" style="width:'.$maxdua.'"><p>'.$maxdua.'</p></div></div></td>
                	<td>% of server usage</td>
                    <td colspan="2"><div id="progress" class="graph"><div id="bar'.$dcdsa.'" style="width:'.$maxdsa.'"><p>'.$maxdsa.'</p></div></div></td>
                </tr>
         </table>';
                             
			           
#get vhosts
      $end.='<b>Virtual Hosts</b><br>';
			$sql   = "SELECT * FROM x_vhosts WHERE vh_acc_fk ='".$userid."' AND vh_deleted_ts IS NULL ORDER BY vh_name_vc ASC";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
      $total =$zdbh->query($sql)->rowCount();

      
            if ($total > 0){ 
$end.='            
			<table class="table table-striped">
            	<tr>
                	<th>Domain</th><th>Directory</th><th><center>Type</center></th><th>Status</th><th width="95px">Created</th>
                </tr>';
        do{ 
        
        $end.='	<tr>
                	<td><a href="http://'.$row['vh_name_vc'].'" target="_blank">'.$row['vh_name_vc'].'</td>
                    <td>'.$row['vh_directory_vc'].'</td>';
                    
                   
           	if ($row['vh_type_in'] == 1){
							$type='<center><img title="Domain" src="modules/zgodx/images/domain.png"></center>';
							}
              elseif ($row['vh_type_in'] == 2){	
							$type='<center><img title="Subdomain" src="modules/zgodx/images/subdomain.png"></center>';
							}else{
							$type='<center><img title="Parked domain" src="modules/zgodx/images/parked.png"></center>';
							} 
                    
            $end.='        <td>'.$type.'</td>';
                    
						if ($GQBU > $GQBM){
								$status="<font color=\"red\">LOCKED</font>";
							}else{
								if ($row['vh_active_in'] == 1){
								//$status="<font color=\"green\">Live ".$bstatus."</font>";
								$status="<font color=\"green\">Live</font>";
								}else{
								$status="<font color=\"orange\">Pending</font>";
								} 
							}
                            
              $end.='      <td>'.$status.'</td>
      				<td>'.date("M d, Y",$row['vh_created_ts']).'</td>
               </tr>';
      } while($row = $list->fetch());
      
      $end.='     </table>';
 }else{
  $end.= "No Virtual Hosts at this time<br />";} 

	$end .='<br />';
	
#get packages
$end.='<b>Reseller Packages</b><br>';
$sql   = "SELECT * FROM x_packages WHERE pk_reseller_fk ='".$userid."' AND pk_deleted_ts IS NULL ORDER BY pk_name_vc ASC";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();
if ($total > 0){

$end.='
        	<table class="table table-striped">
            	<tr>
                    <th colspan="1">Package Name</th>
                    <th colspan="1">Is Admin</th>
                    <th colspan="1">PHP</th>
                    <th colspan="1">CGI</th>
                    <th colspan="1">Created</th>
                    <th colspan="1">Clients</th>
                </tr>';
     do{
    
		if($row['pk_name_vc'] == "LOCKED ACCOUNTS"){
		   $name = "<font color=\"red\">".$row['pk_name_vc']."</font>";
		}else{
		   $name = $row['pk_name_vc'];
		}
		
		if($name == 'Administration'){ // if no changes for group name 1 :)
    $is_admin = 'YES';
    } else{
    $is_admin = 'NO';
    }
		

$end.='
    			<tr>
      				<td>'.$name.'</td>
                    <td>'.$is_admin.'</td>';
                    
              if ($row['pk_enablephp_in'] == 1){
							$php="YES";
							}else{
							$php="NO";
							} 
                    
              $end.='<td>'.$php.'</td>';

              if ($row['pk_enablecgi_in'] == 1){
							$cgi="YES";
							}else{
							$cgi="NO";
							}
                    
              $end.='<td>'.$cgi.'</td>';
                    
                    
							if (empty($row['pk_created_ts'])){
							$crt= "on install";
							}else{ 
							$crt= date("M d, Y",$row['pk_created_ts']);
							}
							
							$end .='<td>'.$crt.'</td>';
							
					$end.='<td><a href="#linkpk_'.$row['pk_name_vc'].'" title="Show clients on package" onclick="toggle_visibility(\'pk_'.$row['pk_name_vc'].'\');"><img src="modules/zgodx/images/show.png" border="0" /></a></td>
    		   </tr>
                <tr>
                	<td colspan="6">';
              
              $sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk ='".$userid."' AND ac_package_fk ='".$row['pk_id_pk']."' AND ac_deleted_ts IS NULL";
              $listpku = $zdbh->query($sql);
              $listpku->setFetchMode(PDO::FETCH_ASSOC);
              $rowpku   = $listpku->fetch();
              
              $end.='<div id="pk_'.$row['pk_name_vc'].'" style="display:none;">';

					
					do{ 
					$end .='<a href="javascript:void(0)" onClick="document.frmMain.getMain.value=\'user\'; document.frmMain.getUser.value=\''.$rowpku['ac_id_pk'].'\'; document.frmMain.submit();">'.$rowpku['ac_user_vc'].'<br>';
					
					 } while($rowpku = $listpku->fetch());
					
          $end.='<br /><br /><br />
                    </div>
                    </td>
           		</tr>';
           		
       } while($row = $list->fetch());
      
      $end.='</table>';
 }else{ 
 $end.= "No Packages at this time<br />";
 } 
 
 $end .='<br />';

#get databases
$end.='<b>MySQL Databases</b> <a href="apps/phpmyadmin/" target="_blank">(Launch phpMyAdmin)</a><br>';
$sql   = "SELECT * FROM x_mysql_databases WHERE my_acc_fk ='".$userid."' AND my_deleted_ts IS NULL ORDER BY my_name_vc ASC";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();
if ($total > 0){

$end.='
        	<table class="table table-striped">
            	<tr>
                    <th colspan="1">Database</th>
                    <th colspan="2">Disk Usage</th>
                    <th colspan="1">Created</th>
                </tr>';
   do{
    
	$space   = FormatFileSize($row['my_usedspace_bi']);
	$space   = explode(" ", $space);

   $end.=' 			<tr>
                    <td>'.$row['my_name_vc'].'</td>    
                    <td>'.$space[0].'</td>
                    <td><b>'.$space[1].'</b></td>
      				<td>'.date("M d, Y",$row['my_created_ts']).'</td>
    			</tr>';
      } while($row = $list->fetch());
		$end.='</table>';
		
 }else{ 
 $end.= "No Databases at this time<br />";
 } 
	$end.='<br />';
	
#get Mailboxes
$end.='<b>Email Accounts</b><br>';
$sql   = "SELECT * FROM x_mailboxes WHERE mb_acc_fk ='".$userid."' AND mb_deleted_ts IS NULL";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();
if ($total > 0){

$end.='
        	<table class="table table-striped">
            	<tr>
                    <th colspan="1">Email Address</th>
                    <th colspan="1">Status</th>
                    <th colspan="1">Created</th>
                    <th colspan="1"><center>On/Off</center></th>
                </tr>';
  do{
    
	$sql     = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['mb_acc_fk']."'";
			$listacc  = $zdbh->query($sql);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch();
    
      $sql = "SELECT mb_created_ts, mb_enabled_in FROM x_mailboxes WHERE mb_address_vc ='".$row['mb_address_vc']."'";
	      $listhm = $zdbh->query($sql);
				$listhm->setFetchMode(PDO::FETCH_ASSOC);
				$rowahm   = $listhm->fetch();
				
    		$end.='	<tr>
      				<td>'.$row['mb_address_vc'].'</td>';
                    
		if ($rowahm['mb_enabled_in'] == 1){		
				$status="<font color=\"green\">Enabled</font>";
				}else{
				$status="<font color=\"red\">Disabled</font>";}
      	
      		$end.='			<td>'.$status.'</td>
                    
      				<td>'.date("M d, Y",$row['mb_created_ts']).'</td>
                    <td>';
                    
       if ($rowahm['mb_enabled_in'] == 1){	
             $end.='       					<a title="Lock Email Account" href="javascript:void(0)"
                    							onClick="document.frmMain.getMain.value=\'user\';
                                                document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\';
                                                document.frmMain.getToggleMail.value=\'MAILOFF\';
                                                document.frmMain.getToggleMailID.value=\''.$row['mb_address_vc'].'\';
                                                document.frmMain.submit();"><center><img src="modules/zgodx/images/locksmall.png" border="0" /></center></a>';
         }else{ 
               $end.='           				 <a title="Unlock Email Account" href="javascript:void(0)"
                    							onClick="document.frmMain.getMain.value=\'user\';
                                                document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\';	
                                                document.frmMain.getToggleMail.value=\'MAILON\';
                                                document.frmMain.getToggleMailID.value=\''.$row['mb_address_vc'].'\';
                                                document.frmMain.submit();"><center><img src="modules/zgodx/images/unlocksmall.png" border="0" /></center></a>';
           } 
           $end.='         </td>
    			</tr>';
    			
          } while($row = $list->fetch());
		$end.='	</table>';
            
 }else{ 
 $end.= "No mailboxes at this time<br />";} 
 
	$end.='<br />';
	          
#get forwards
$end.='<b>Mail Forwarders</b><br>';
$sql = "SELECT * FROM x_forwarders WHERE fw_acc_fk ='".$userid."' AND fw_deleted_ts IS NULL";
			$listforwarders  = $zdbh->query($sql);
			$listforwarders->setFetchMode(PDO::FETCH_ASSOC);
			$rowforwarders   = $listforwarders->fetch();
$totalforwarders =$zdbh->query($sql)->rowCount();
if ($totalforwarders > 0){

$end.='
        	<table class="table table-striped">
            	<tr>
                    <th colspan="1">From Email</th>
                    <th colspan="1">To Email</th>
                    <th colspan="1">Created</th>
                </tr>';
    do{
    	$end.='		<tr>
      				<td>'.$rowforwarders['fw_address_vc'].'</td>
      				<td>'.$rowforwarders['fw_destination_vc'].'</td>
      				<td>'.date("M d, Y",$rowforwarders['fw_created_ts']).'</td>
    			</tr>';
    } while($rowforwarders = $listforwarders->fetch());
		$end.='</table>';
                 
 }else{ 
 $end.= "No mail forwards at this time<br />";}
 
	$end.='<br />';

#get aliases
$end.='<b>Aliases</b><br>';
$sql   = "SELECT * FROM x_aliases WHERE al_acc_fk ='".$userid."' AND al_deleted_ts IS NULL";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();
if ($total > 0){

$end.='
        	<table class="table table-striped">
            	<tr>
                    <th colspan="1">Real Email</th>
                    <th colspan="1">Virtual Email</th>
                    <th colspan="1">Created</th>
                </tr>';
    do{
    
    		$end.='	<tr>
      				<td>'.$row['al_address_vc'].'</td>
      				<td>'.$row['al_destination_vc'].'</td>
      				<td>'.date("M d, Y",$row['al_created_ts']).'</td>
    			</tr>';
     } while($row = $list->fetch());
		$end.='</table>';
		
	}else{ 
 $end.= "No mail aliases at this time<br />";
 } 
	$end.='<br />';
	
	#get distribution lists
	$end.='<b>Distribution Lists</b><br>';
	$sql   = "SELECT * FROM x_distlists WHERE dl_acc_fk ='".$userid."' AND dl_deleted_ts IS NULL";
				$list  = $zdbh->query($sql);
				$list->setFetchMode(PDO::FETCH_ASSOC);
				$row   = $list->fetch();
	$total =$zdbh->query($sql)->rowCount();
	# tg - fix for empty distribution lists
	if (isset($row['dl_acc_fk']))
	{
	$sqlacc = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['dl_acc_fk']."'";
			$listacc  = $zdbh->query($sqlacc);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch();
	}
	else
	{
		$rowacc = NULL;
	}
if ($total > 0){

$end.='
        	<table class="table table-striped">
            	<tr>
                    <th colspan="1">Address</th>
                    <th colspan="1">Created</th>
                    <th colspan="1"><center>Recipients</center></th>
                </tr>';
    do{
    	$end.='<tr>
      				<td>'.$row['dl_address_vc'].'</td>                            
                    <td>'.date("M d, Y",$row['dl_created_ts']).'</td>
                    <td><center><a href="#link_'.$rowacc['ac_id_pk'].'" title="Show Mail List Users" onclick="toggle_visibility(\'ex_'.$row['dl_address_vc'].'\');"><img src="modules/zgodx/images/show.png" border="0" /></a></center></td>
    			</tr>
                <tr>
                	<td colspan="3">';
              $sqldlu = "SELECT * FROM x_distlistusers WHERE du_distlist_fk ='" .$row['dl_id_pk']."'";
	      $listdlu = $zdbh->query($sqldlu);
				$listdlu->setFetchMode(PDO::FETCH_ASSOC);
				$rowdlu   = $listdlu->fetch();
				
        $end.='<div id="ex_'.$row['dl_address_vc'].'" style="display:none;">';
				
				 do{ 
							$end.= $rowdlu['du_address_vc'].'<br>';
							} while($rowdlu = $listdlu->fetch());   
							               		
		   		$end.='	<br /><br /><br />
                    </div>
                    </td>
           		</tr>';
           		
     } while($row = $list->fetch());
     
		$end.='	</table>';
 }else{ 
 $end.= "No Distribution Lists at this time<br />";
 } 
	$end.='<br />';
   
#get ftp accounts
$end.='<b>FTP Accounts</b><br>';
$sql   = "SELECT * FROM x_ftpaccounts WHERE ft_acc_fk ='".$userid."' AND ft_deleted_ts IS NULL";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();
if ($total > 0){

$end.='
        	<table class="table table-striped">
            	<tr>
                	<th colspan="1">Login Name</th>
                    <th colspan="1">Directory</th>
                    <th colspan="1">Permission</th>
                    <th colspan="1">Created</th>
                    <th colspan="1"><center>Actions</center></th>
                </tr>';
     do{
    	$end.='		<tr>
                    <td>'.$row['ft_user_vc'].'</td>
                    <td>'.$row['ft_directory_vc'].'</td>    
                    <td>'.$row['ft_access_vc'].'</td>
      				<td>'.date("M d, Y",$row['ft_created_ts']).'</td>
                    <td><a title="Delete User FTP Account" href="javascript:void(0)"
                    							onClick="document.frmMain.getMain.value=\'user\'; 
                                                document.frmMain.getUser.value=\''.$rowac['ac_id_pk'].'\';	
                                                document.frmMain.getDeleteFTP.value=\'DELETEFTP\';
                                                document.frmMain.getDeleteFTPACC.value=\''.$rowac['ac_id_pk'].'\';
                                                document.frmMain.getDeleteFTPUSER.value=\''.$row['ft_user_vc'].'\';
                                                document.frmMain.getDeleteFTPID.value=\''.$row['ft_id_pk'].'\';
                                                document.frmMain.submit();"><center><img src="modules/zgodx/images/deletesmall.png" border="0" /></center></a></td>
    			</tr>';
    } while($row = $list->fetch());
		$end.='</table>';
 }else{ 
 $end.= "No FTP accounts at this time<br />";
 }
	$end.='<br />';
        
#get cron jobs
$end.='<b>Cron Jobs</b><br>';
$sql = "SELECT * FROM x_cronjobs WHERE ct_acc_fk ='".$userid."' AND ct_deleted_ts IS NULL";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();
if ($total > 0){

$end.='
        	<table class="table table-striped">
            	<tr>
                    <th colspan="1">Running Script</th>
                    <th colspan="1">Description</th>
                    <th colspan="1">Created</th>
					<th colspan="1">Delete</th>
                </tr>';
     do{
    	$end.='		<tr>
      				<td>'.$row['ct_script_vc'].'</td>
                    <td>'.$row['ct_description_tx'].'</td>                            
                    <td>'.date("M d, Y",$row['ct_created_ts']).'</td>
					<td><a title="Delete User Cron" href="javascript:void(0)"
                    							onClick="document.frmMain.getMain.value=\'user\';
												document.frmMain.getUser.value=\''.$rowac['ac_id_pk'].'\';
                                                document.frmMain.getDeleteCRON.value=\'DELETECRON\';
                                                document.frmMain.getDeleteCRONUSERID.value=\''.$rowac['ac_id_pk'].'\';
                                                document.frmMain.getDeleteCRONID.value=\''.$row['ct_id_pk'].'\';
                                                document.frmMain.submit();"><center><img src="modules/zgodx/images/deletesmall.png" border="0" /></center></a></td>
    			</tr>';
    } while($row = $list->fetch());
		$end.='	</table>';
		
  }else{ 
  $end.= "No Cron Jobs at this time<br />";
  } 
	$end.='		<br />  
            
                                            
		</td>
	</tr>
</table>

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

return $end;
}
?>