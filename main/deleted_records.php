<?php 
function zGodX_main_data(){
      $end ='';

			$user_array = ctrl_users::GetUserDetail();
			$user_x  = $user_array['username'];
			$user_x_id = $user_array['userid'];
      global $zdbh;
      
      if (isset($_POST['getUnDel'])){ $end.= xUnDelete($_POST['getUnDel'], $_POST['getUnDelID']);} 
      
if ($user_x == "zadmin" || $user_x_id ==1){ #If zadmin we get everything
$end.='<em>zGodX &raquo; <b>Deleted Records</b></em><br><br>';
                                     
#get vhosts
$end.='<b>Virtual Hosts</b><br>';
			$sql   = "SELECT * FROM x_vhosts WHERE vh_deleted_ts IS NOT NULL ORDER BY vh_name_vc ASC";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
      $total =$zdbh->query($sql)->rowCount();

            if ($total > 0){
            
      	$sql = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['vh_acc_fk']."'";
			$listacc  = $zdbh->query($sql);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch();      
             
$end.='<table class="table table-striped">
            	<tr>
                	<th>Account</th>
                	<th>Domain</th>
                	<th>Directory</th>
                	<th><center>Type</center></th>
                	<th>Deleted</th>
                	<th width="65px">Actions</th>
                </tr>';
        do{ 
        
          $end.='<tr>
                  <td><a href="javascript:void(0)" onClick="document.frmMain.getMain.value=\'user\'; document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\'; document.frmMain.submit();">'.$rowacc['ac_user_vc'].'</a></td>
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
                    
            $end.='        <td>'.$type.'</td>
      				<td>'.date("M d, Y",$row['vh_deleted_ts']).'</td>
      				
      				<td><a title="Restore Virtual Host" href="javascript:void(0)"
                    							onClick="document.frmMain.getMain.value=\'deleted_records\'; 	
                                                document.frmMain.getUnDel.value=\'VHOST\';
                                                document.frmMain.getUnDelID.value=\''.$row['vh_id_pk'].'\';
                                                document.frmMain.submit();"><center><img src="modules/zgodx/images/restore.png" border="0"/></center></a>
      				
      				</td>
               </tr>';
      } while($row = $list->fetch());
      
      $end.='     </table>';
 }else{
  $end.= "<br />No Virtual Hosts at this time<br />";} 

	$end .='<br />';
	
#get packages
$end.='<b>Packages</b><br>';

$sql   = "SELECT * FROM x_packages WHERE pk_deleted_ts IS NOT NULL ORDER BY pk_name_vc ASC";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();
if ($total > 0){

      	$sql = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['pk_reseller_fk']."'";
			$listacc  = $zdbh->query($sql);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch(); 

$end.='
        	<table class="table table-striped">
            	<tr>
                    <th colspan="1">Account</th>
                    <th colspan="1">Package Name</th>
                    <th colspan="1">Is Admin</th>
                    <th colspan="1">PHP</th>
                    <th colspan="1">CGI</th>
                    <th colspan="1">Deleted</th>
                    <th colspan="1" width="65px">Actions</th>
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
    			<td><a href="javascript:void(0)" onClick="document.frmMain.getMain.value=\'user\'; document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\'; document.frmMain.submit();">'.$rowacc['ac_user_vc'].'</a></td>
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
                    
                    
							if (empty($row['pk_deleted_ts'])){
							$crt= "on install";
							}else{ 
							$crt= date("M d, Y",$row['pk_created_ts']);
							}
							
							$end .='<td>'.$crt.'</td>';
							
					$end.='<td>
					<a href="#linkpk_'.$row['pk_name_vc'].'" title="Show clients on package" onclick="toggle_visibility(\'pk_'.$row['pk_name_vc'].'\');"><img src="modules/zgodx/images/show.png" border="0"/></a>  
					<a title="Restore Package" href="javascript:void(0)"
                                                onClick="document.frmMain.getMain.value=\'deleted_records\'; 	
                                                document.frmMain.getUnDel.value=\'PACKAGE\';
                                                document.frmMain.getUnDelID.value=\''.$row['pk_id_pk'].'\';
                                                document.frmMain.submit();"><center><img src="modules/zgodx/images/restore.png" border="0"/></center></a>
    		  </td>
    		   </tr>
                <tr>
                	<td colspan="7">';
              
              $sql = "SELECT * FROM x_accounts WHERE ac_package_fk ='".$row['pk_id_pk']."' AND ac_deleted_ts IS NOT NULL";
              $listpku = $zdbh->query($sql);
              $listpku->setFetchMode(PDO::FETCH_ASSOC);
              $rowpku   = $listpku->fetch();
              
              $end.='<div id="pk_'.$row['pk_name_vc'].'" style="display:none;">';

					
					do{ 
					$end .='<a href="javascript:void(0)" onClick="document.frmMain.getMain.value=\'user\'; document.frmMain.getUser.value=\''.$rowpku['ac_id_pk'].'\'; document.frmMain.submit();">'.$rowpku['ac_user_vc'].'</a><br>';
					
					 } while($rowpku = $listpku->fetch());
					
          $end.='<br /><br /><br />
                    </div>
                    </td>
           		</tr>';
           		
       } while($row = $list->fetch());
      
      $end.='</table>';
 }else{ 
 $end.= "<i>No deleted packages at this time</i><br />";
 } 
 
 $end .='<br />';

#get databases
$end.='<b>MySQL Databases</b> <a href="apps/phpmyadmin/" target="_blank">(Launch phpMyAdmin)</a><br>';
$sql   = "SELECT * FROM x_mysql_databases WHERE my_deleted_ts IS NOT NULL ORDER BY my_name_vc ASC";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();
if ($total > 0){


      	$sql = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['my_acc_fk']."'";
			$listacc  = $zdbh->query($sql);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch(); 

$end.='
        	<table class="table table-striped">
            	<tr>
                    <th colspan="1">Account</th>
                    <th colspan="1">Database</th>
                    <th colspan="2">Disk Usage</th>
                    <th colspan="1">Deleted</th>
                     <th colspan="1" width="65px">Action</th>
                </tr>';
   do{
    
	$space   = FormatFileSize($row['my_usedspace_bi']);
	$space   = explode(" ", $space);

   $end.=' 			<tr>
                    <td><a href="javascript:void(0)" onClick="document.frmMain.getMain.value=\'user\'; document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\'; document.frmMain.submit();">'.$rowacc['ac_user_vc'].'</a></td>
                    <td>'.$row['my_name_vc'].'</td>    
                    <td>'.$space[0].'</td>
                    <td><b>'.$space[1].'</b></td>
      				<td>'.date("M d, Y",$row['my_deleted_ts']).'</td>
      				<td>
      									<a title="Restore DB" href="javascript:void(0)"
                    							onClick="document.frmMain.getMain.value=\'deleted_records\'; 	
                                                document.frmMain.getUnDel.value=\'DATABASE\';
                                                document.frmMain.getUnDelID.value=\''.$row['my_id_pk'].'\';
                                                document.frmMain.submit();"><center><img src="modules/zgodx/images/restore.png" border="0"/></center></a>
              </td>                                  
    			</tr>';
      } while($row = $list->fetch());
		$end.='</table>';
		
 }else{ 
 $end.= "<i>No deleted databases at this time</i><br />";
 } 
	$end.='<br />';
	
#get Mailboxes
$end.='<b>Email Accounts</b><br>';

$sql   = "SELECT * FROM x_mailboxes WHERE mb_deleted_ts IS NOT NULL";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();
if ($total > 0){

	$sql     = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['mb_acc_fk']."'";
			$listacc  = $zdbh->query($sql);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch();

$end.='
        	<table class="table table-striped">
            	<tr>
                    <th colspan="1">Account</th>
                    <th colspan="1">Email Address</th>
                    <th colspan="1">Deleted</th>
                     <th colspan="1" width="65px">Action</th>
                </tr>';
  do{
       
				
    		$end.='	<tr>
              <td><a href="javascript:void(0)" onClick="document.frmMain.getMain.value=\'user\'; document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\'; document.frmMain.submit();">'.$rowacc['ac_user_vc'].'</a></td>
      				<td>'.$row['mb_address_vc'].'</td>
                    
                    
      				<td>'.date("M d, Y",$row['mb_deleted_ts']).'</td>
                    <td>';
                    
               $end.='<a title="Restore Email Account" href="javascript:void(0)"
                    		onClick="document.frmMain.getMain.value=\'deleted_records\'; 	
                        document.frmMain.getUnDel.value=\'EMAILACC\';
                        document.frmMain.getUnDelID.value=\''.$row['mb_id_pk'].'\';
                        document.frmMain.submit();"><center><img src="modules/zgodx/images/restore.png" border="0"/></center></a>
                   </td>
    			</tr>';
    			
          } while($row = $list->fetch());
		$end.='	</table>';
            
 }else{ 
 $end.= "<i>No deleted mailboxes at this time</i><br />";} 
 
	$end.='<br />';
	          
#get forwards
$end.='<b>Mail Forwarders</b><br>';

$sql = "SELECT * FROM x_forwarders WHERE fw_deleted_ts IS NOT NULL";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();
if ($total > 0){

      $sql = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['fw_acc_fk']."'";
			$listacc  = $zdbh->query($sql);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch(); 

$end.='
        	<table class="table table-striped">
            	<tr>
                    <th colspan="1">Account</th>
                    <th colspan="1">From Email</th>
                    <th colspan="1">To Email</th>
                    <th colspan="1">Deleted</th>
                     <th colspan="1" width="65px">Action</th>
                </tr>';
    do{
    	$end.='		<tr>
              <td><a href="javascript:void(0)" onClick="document.frmMain.getMain.value=\'user\'; document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\'; document.frmMain.submit();">'.$rowacc['ac_user_vc'].'</a></td>
      				<td>'.$row['fw_address_vc'].'</td>
      				<td>'.$row['fw_destination_vc'].'</td>
      				<td>'.date("M d, Y",$row['fw_deleted_ts']).'</td>
      				<td><a title="Restore Email Forwarder" href="javascript:void(0)"
                    		onClick="document.frmMain.getMain.value=\'deleted_records\'; 	
                        document.frmMain.getUnDel.value=\'EMAILFWD\';
                        document.frmMain.getUnDelID.value=\''.$row['fw_id_pk'].'\';
                        document.frmMain.submit();"><center><img src="modules/zgodx/images/restore.png" border="0"/></center></a>
             </td>          
    			</tr>';
    } while($row = $list->fetch());
		$end.='</table>';
                 
 }else{ 
 $end.= "<i>No deleted mail forwards at this time</i><br />";}
 
	$end.='<br />';

#get aliases
$end.='<b>Aliases</b><br>';

$sql   = "SELECT * FROM x_aliases WHERE al_deleted_ts IS NOT NULL";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();
if ($total > 0){

      $sql = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['al_acc_fk']."'";
			$listacc  = $zdbh->query($sql);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch(); 

$end.='
        	<table class="table table-striped">
            	<tr>
                    <th colspan="1">Account</th>
                    <th colspan="1">Real Email</th>
                    <th colspan="1">Virtual Email</th>
                    <th colspan="1">Deleted</th>
                     <th colspan="1" width="65px">Action</th>
                </tr>';
    do{
    
    		$end.='	<tr>
              <td><a href="javascript:void(0)" onClick="document.frmMain.getMain.value=\'user\'; document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\'; document.frmMain.submit();">'.$rowacc['ac_user_vc'].'</a></td>
      				<td>'.$row['al_address_vc'].'</td>
      				<td>'.$row['al_destination_vc'].'</td>
      				<td>'.date("M d, Y",$row['al_deleted_ts']).'</td>
      				<td><a title="Restore Email Alias" href="javascript:void(0)"
                    		onClick="document.frmMain.getMain.value=\'deleted_records\'; 	
                        document.frmMain.getUnDel.value=\'ALIAS\';
                        document.frmMain.getUnDelID.value=\''.$row['al_id_pk'].'\';
                        document.frmMain.submit();"><center><img src="modules/zgodx/images/restore.png" border="0"/></center></a>
             </td>      				
    			</tr>';
     } while($row = $list->fetch());
		$end.='</table>';
		
 }else{ 
 $end.= "<i>No deleted mail aliases at this time</i><br />";
 } 
	$end.='<br />';
	
#get distribution lists
$end.='<b>Distribution Lists</b><br>';

$sql   = "SELECT * FROM x_distlists WHERE dl_deleted_ts IS NOT NULL";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();

	$sqlacc = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['dl_acc_fk']."'";
			$listacc  = $zdbh->query($sqlacc);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch();

if ($total > 0){

$end.='
        	<table class="table table-striped">
            	<tr>
                    <th colspan="1">Address</th>
                    <th colspan="1">Deleted</th>
                    <th colspan="1" width="65px">Actions</th>
                </tr>';
    do{
    	$end.='<tr>
      				<td>'.$row['dl_address_vc'].'</td>                            
                    <td>'.date("M d, Y",$row['dl_deleted_ts']).'</td>
                    <td>
                    <a href="#dl_'.$rowacc['ac_id_pk'].'" title="Show Mail List Users" onclick="toggle_visibility(\'ex_'.$row['dl_address_vc'].'\');"><img src="modules/zgodx/images/show.png" border="0"/></a>
                    <a title="Restore Distribution List" href="javascript:void(0)"
                    		onClick="document.frmMain.getMain.value=\'deleted_records\'; 	
                        document.frmMain.getUnDel.value=\'DISTLIST\';
                        document.frmMain.getUnDelID.value=\''.$row['dl_id_pk'].'\';
                        document.frmMain.submit();"><center><img src="modules/zgodx/images/restore.png" border="0"/></center></a>
                    </td>
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
 $end.= "<i>No deleted distribution lists at this time</i><br />";
 } 
	$end.='<br />';
   
#get ftp accounts
$end.='<b>FTP Accounts</b><br>';

$sql   = "SELECT * FROM x_ftpaccounts WHERE ft_deleted_ts IS NOT NULL";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();
if ($total > 0){

	$sqlacc = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['ft_acc_fk']."'";
			$listacc  = $zdbh->query($sqlacc);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch();

$end.='
        	<table class="table table-striped">
            	<tr>
                	<th colspan="1">Account</th>
                	<th colspan="1">Login Name</th>
                    <th colspan="1">Directory</th>
                    <th colspan="1">Permission</th>
                    <th colspan="1">Deleted</th>
                    <th colspan="1" width="65px">Action</th>
                </tr>';
     do{
    	$end.='		<tr>
                    <td><a href="javascript:void(0)" onClick="document.frmMain.getMain.value=\'user\'; document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\'; document.frmMain.submit();">'.$rowacc['ac_user_vc'].'</a></td>
                    <td>'.$row['ft_user_vc'].'</td>
                    <td>'.$row['ft_directory_vc'].'</td>    
                    <td>'.$row['ft_access_vc'].'</td>
      				<td>'.date("M d, Y",$row['ft_deleted_ts']).'</td>
                    <td>
                                        <a title="Restore FTP Account" href="javascript:void(0)"
                    		onClick="document.frmMain.getMain.value=\'deleted_records\'; 	
                        document.frmMain.getUnDel.value=\'FTPACC\';
                        document.frmMain.getUnDelID.value=\''.$row['ft_id_pk'].'\';
                        document.frmMain.submit();"><center><img src="modules/zgodx/images/restore.png" border="0"/></center></a>
                        </td>
			</tr>';
    } while($row = $list->fetch());
		$end.='</table>';
 }else{ 
 $end.= "<i>No deleted FTP accounts at this time</i><br />";
 }
	$end.='<br />';
        
#get cron jobs
$end.='<b>Cron Jobs</b><br>';

$sql = "SELECT * FROM x_cronjobs WHERE ct_deleted_ts IS NOT NULL";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
$total =$zdbh->query($sql)->rowCount();
if ($total > 0){

      	$sql = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['ct_acc_fk']."'";
			$listac  = $zdbh->query($sql);
			$listac->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listac->fetch(); 

$end.='
        	<table class="table table-striped">
            	<tr>
                    <th colspan="1">Account</th>
                    <th colspan="1">Running Script</th>
                    <th colspan="1">Description</th>
                    <th colspan="1">Deleted</th>
                    <th colspan="1" width="65px">Action</th>
                </tr>';
     do{
    	$end.='		<tr>
                    <td><a href="javascript:void(0)" onClick="document.frmMain.getMain.value=\'user\'; document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\'; document.frmMain.submit();">'.$rowacc['ac_user_vc'].'</a></td>
                    <td>'.$row['ct_script_vc'].'</td>
                    <td>'.$row['ct_description_tx'].'</td>                            
                    <td>'.date("M d, Y",$row['ct_deleted_ts']).'</td>
                    <td>
                    <a title="Restore Cron Job" href="javascript:void(0)"
                    		onClick="document.frmMain.getMain.value=\'deleted_records\'; 	
                        document.frmMain.getUnDel.value=\'CRON\';
                        document.frmMain.getUnDelID.value=\''.$row['ct_id_pk'].'\';
                        document.frmMain.submit();"><center><img src="modules/zgodx/images/restore.png" border="0"/></center></a>
    			</tr>';
    } while($row = $list->fetch());
		$end.='	</table>';
		
  }else{ 
  $end.= "<i>No deleted cron jobs at this time</i><br />";
  } 
	$end.='		<br />  ';
            
$end.='
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
$end.='
<em>zGodX &raquo; <b>Deleted Records</b></em><br><br>
<b>Only administrator can restore deleted stuff. Please contact your administrator.</b>';
}

return $end;
}
?>