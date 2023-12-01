<?php
// lšctž UTF-8
// #################################################################################### ZPANEL FUNCTIONS

# return alert
function shout_me($message, $class = "danger", $thelog="") {
if ($message=='' && $class=='') {
      $line='';
      }else {
        $line = "<div class=\"alert alert-block alert-".$class."\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button><p>" . $message . "</p></div>"; 
        
        if ($thelog!=""){
        $line.= '
        <div id="hidden_log" style="display:none">
        <a href="#" id="close1" title="Close zDeamon LOG"><button type=\"button\" class=\"close\">x</button></a>
        <b>zDaemon output log:</b><br />
        <br />'.$thelog.'
        <br />
        <a href="#" id="close2" title="Close zDeamon LOG"><button type=\"button\" class=\"close\">x</button></a>
        <br />
        </div>
        ';
        }

       } 
        return $line;
}

# for 10.1.1
    function xgetLastRunTime()
    {
        $time = ctrl_options::GetSystemOption('daemon_lastrun');
        if ($time != '0') {
            return date(ctrl_options::GetSystemOption('zpanel_df'), $time);
        } else {
            return false;
        }
    }

    function xgetNextRunTime()
    {
        if (ctrl_options::GetSystemOption('daemon_lastrun') > 0) {
            $new_time = ctrl_options::GetSystemOption('daemon_lastrun') + ctrl_options::GetSystemOption('daemon_run_interval');
            return date(ctrl_options::GetSystemOption('zpanel_df'), $new_time);
        } else {
            // The default cron is set to run every 5 minutes on the 5 minute mark!
            return date(ctrl_options::GetSystemOption('zpanel_df'), ceil(time() / 300) * 300);
        }
    }


function curr_url() {
  $protocol = 'http';
  if ($_SERVER['SERVER_PORT'] == 443 || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')) {
    $protocol .= 's';
    $protocol_port = $_SERVER['SERVER_PORT'];
  } else {
    $protocol_port = 80;
  }
  $host = $_SERVER['HTTP_HOST'];
  $port = $_SERVER['SERVER_PORT'];
  $request = $_SERVER['PHP_SELF'];
  $query = isset($_SERVER['argv']) ? substr($_SERVER['argv'][0], strpos($_SERVER['argv'][0], ';') + 1) : '';
  $toret = $protocol . '://' . $host . ($port == $protocol_port ? '' : ':' . $port) . $request . (empty($query) ? '' : '?' . $query);
  return $toret;
}
 $srvname = $_SERVER['SERVER_NAME'];
 $srvaddr = $_SERVER['SERVER_ADDR'];
 $currenturl = curr_url();




# FORMAT SIZE
function FormatFileSize($a) {
    # DESCRIPTION: Formats bytes into a human readable string.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    $size = $a;
    if ($size / 1024000000 > 1) {
        $fretval = round($size / 1024000000, 1) . ' GB';
    } elseif ($size / 1024000 > 1) {
        $fretval = round($size / 1024000, 1) . ' MB';
    } elseif ($size / 1024 > 1) {
        $fretval = round($size / 1024, 1) . ' KB';
    } else {
        $fretval = round($size, 1) . ' bytes';
    }
    return $fretval;
}

# ADD TO LOG
function xTriggerLog($a=0, $b="No details.") {
    # DESCRIPTION: Logs an event, for debugging or audit purposes in the 'x_logs' table.
    # FUNCTION RELEASE: 10.0.2
    global $zdbh;
    $acc_key = $a;
    $log_details = $b;
    $sql = "INSERT INTO x_logs (lg_user_fk, lg_detail_tx) VALUES (" . $acc_key . ", '" . $log_details . "')";
    $zdbh->prepare($sql)->execute();

}


# START STOP, RESTART SERVICE
function ToggleService($a){ 
$end='';

	if ($a == "zDAEMON"){
		if (sys_versions::ShowOSPlatformVersion() == "Windows") {
		exec('C:/ZPanel/bin/php/php.exe C:/ZPanel/panel/bin/daemon.php', $output);
		}else{
		exec('php -q /etc/zpanel/panel/bin/daemon.php', $output);
		}
		sleep(5);
		//$end.='<b>Daemon output log:</b><br />';	
		$message='';
      foreach ($output as $line)
      {
   		 $message.= "<br/>$line\n";
			}
		$end.= 'Daemon run complete, see LOG!';
		//sleep(10);
		return shout_me($end,"success", $message);	
	}


	if ($a == "STOPFTP"){
		if (sys_versions::ShowOSPlatformVersion() == "Windows") {
		exec('NET STOP "FileZilla Server"', $output);
		} else {
		exec('/etc/zpanel/bin/zsudo service proftpd stop', $output);
		sleep(5);
		}
    //	$end.='<b>Command output log:</b><br />';
		//	foreach ($output as $line)
		//	{
   	//		 $end.= "<br/>$line\n";
		//	}
		if (sys_monitoring::PortStatus(21)==0) {
		$end.= 'FTP Service has been <b>STOPPED</b>';
		return shout_me($end,"success");	
		}else{
		$end.= '<b>ERROR STOPPING SERVICE</b>';
		return shout_me($end,"danger");
		}
	}
		
		
	if ($a == "STARTFTP"){
		if (sys_versions::ShowOSPlatformVersion() == "Windows") {
		exec('NET START "FileZilla Server"', $output);
		} else {
		exec('/etc/zpanel/bin/zsudo service proftpd start', $output);
		sleep(5);
		}
    	//$end.='<b>Command output log:</b><br>';
			//foreach ($output as $line)
			//{
   		//	 $end.= "<br/>$line\n";
			//}
		if (sys_monitoring::PortStatus(21)==1) {
		$end.= 'FTP Service has been <b>STARTED</b></div>';
		return shout_me($end,"success");	
		}else{
		$end.='<b>ERROR STARTING SERVICE</b></div>';
		return shout_me($end,"danger");
		}
	}
		
	if ($a == "RESTARTFTP"){
		if (sys_versions::ShowOSPlatformVersion() == "Windows") {
		exec('NET STOP "FileZilla Server"', $output);
		exec('NET START "FileZilla Server"', $output);
		} else {
		exec('/etc/zpanel/bin/zsudo service proftpd restart', $output);
		sleep(5);
		}
    	//$end.='<b>Command output log:</b><br>';
			//foreach ($output as $line)
			//{
   		//	 $end.= "<br/>$line\n";
			//}
		if (sys_monitoring::PortStatus(21)==1) {
		$end.= 'FTP Service has been <b>RESTARTED</b>';
		return shout_me($end,"success");	
		}else{
		$end.= '<b>ERROR RESTARTING SERVICE</b>';
		return shout_me($end,"danger");
		}
	}
		
	if ($a == "STOPHMAIL"){
		if (sys_versions::ShowOSPlatformVersion() == "Windows") {
		exec('NET STOP hMailServer', $output);
		sleep(5);
		}else{
		exec("/etc/zpanel/bin/zsudo service postfix stop", $output);
		sleep(5);
		}
    	//$end.='<b>Command output log:</b><br>';
			//foreach ($output as $line)
			//{
   		//	 $end.= "<br/>$line\n";
			//}
		if (sys_monitoring::PortStatus(25)==0) {
		$end.= 'Mail Service has been <b>STOPPED</b>';
		return shout_me($end,"success");	
		}else{
		$end.= '<b>ERROR STOPPING SERVICE</b>';
		return shout_me($end,"danger");
		}
	}
		
	if ($a == "STARTHMAIL"){
		if (sys_versions::ShowOSPlatformVersion() == "Windows") {
		exec('NET START hMailServer', $output);
		sleep(5);
    	}else{
		exec("/etc/zpanel/bin/zsudo service postfix start", $output);
		sleep(5);
		}
		//$end.='<b>Command output log:</b><br>';
			//foreach ($output as $line)
			//{
   		//	 $end.= "<br/>$line\n";
			//}
		if (sys_monitoring::PortStatus(25)==1) {
		$end.= 'Mail Service has been <b>STARTED</b>';
		return shout_me($end,"success");	
		}else{
		$end.= '<b>ERROR STARTING SERVICE</b>';
		return shout_me($end,"danger");
		}
	}
		
	if ($a == "RESTARTHMAIL"){
		  if (sys_versions::ShowOSPlatformVersion() == "Windows") {
		exec('NET STOP hMailServer', $output);
		exec('NET START hMailServer', $output);
    	}else{
		exec("/etc/zpanel/bin/zsudo service postfix reload", $output);
		sleep(5);
		}
		//$end.='<b>Command output log:</b><br>';
			//foreach ($output as $line)
			//{
   		//	 $end.= "<br/>$line\n";
			//}
		if (sys_monitoring::PortStatus(25)==1) {
		$end.= 'Mail Service has been <b>RESTARTED</b>';
		return shout_me($end,"success");	
		}else{
		$end.= '<b>ERROR RESTARTING SERVICE</b>';
		return shout_me($end,"danger");	
		}
	}
		
	if ($a == "RESTARTAPACHE"){
		if (sys_versions::ShowOSPlatformVersion() == "Windows") {
    		exec("C:\\ZPanel\\bin\\apache\\bin\\httpd.exe -k restart -n \"Apache\"", $output);
		}else{
    		exec("/etc/zpanel/bin/zsudo service " . ctrl_options::GetSystemOption('apache_sn') . " reload", $output);	
		}
		
    	//$end.= '<b>Command output log:</b><br>';
			//foreach ($output as $line)
			//{
   		//	 $end.= "<br/>$line\n";
			//}
			$mess="";
		if (sys_monitoring::PortStatus(80)==1) {
		$end.= 'Apache Service has been <b>RESTARTED</b>';
		$mess.= "success";	
		}else{
		$end.= '<b>ERROR RESTARTING SERVICE</b>';
		$mess.= "danger";	
		}
		return shout_me($end, $mess);
	}
		
	if ($a == "RESTARTMYSQL"){
		if (sys_versions::ShowOSPlatformVersion() == "Windows") {
		exec('C:/ZPanel/panel/modules/admin/zgodx/code/restartmysql.cmd', $output);
    //exec('NET STOP MySQL /Y', $output);
		//exec('NET START MySQL', $output);
    	}else{
      #POSIXs
        if (GetOsName()=="Ubuntu") {
        exec("/etc/zpanel/bin/zsudo service mysql restart", $output); // mysql on ubuntu
        } else {
        exec("/etc/zpanel/bin/zsudo service mysqld restart", $output); // mysqld on centos
        }
      }
		//$end.='<b>Command output log:</b><br>';
			//foreach ($output as $line)
			//{
   		//	 $end.= "<br/>$line\n";
			//}
		//if (sys_monitoring::PortStatus(3306)==1) {
		$end.= 'MySQL Service has been <b>RESTARTED</b>';
		return shout_me($end,"success");	
		//}else{
		//$end.= '<b>ERROR RESTARTING SERVICE</b>';
		//return shout_me($end,"danger");	
		//}	
	}

}


# LOCK, UNLOCK ACCOUNT
function ToggleAcc($a, $b, $c){
  global $zdbh;
	#if there is a LOCK request...
	if($a == "LOCKACC"){
	#get account info and apend password
	$sql  		= "SELECT ac_user_vc, ac_pass_vc, ac_package_fk FROM x_accounts WHERE ac_id_pk='".$b."'";
			$listac  = $zdbh->query($sql);
			$listac->setFetchMode(PDO::FETCH_ASSOC);
			$rowac   = $listac->fetch();
	$username   = $rowac['ac_user_vc'];
	$lockedpass = $rowac['ac_pass_vc'];
	
	if(substr($lockedpass, 0, 8) != "!!!!!!!!"){
	$lockedpass = "!!!!!!!!".$lockedpass;
	$sql 		= "UPDATE x_accounts SET ac_pass_vc ='".$lockedpass."' WHERE ac_id_pk='".$b."'";
	$zdbh->prepare($sql)->execute();	
	
	#lets move them to a LOCKED package
	#first we create a table to hold thier old information if it doesn't already exist
	$sql 	 	= "SHOW TABLES LIKE 'x_zgodx_lockedaccounts'";
	$listtable  =$zdbh->query($sql)->rowCount();
		if($listtable == 0){
  		   $sql_make = "CREATE TABLE IF NOT EXISTS x_zgodx_lockedaccounts (
  					zg_id_pk int(6) unsigned NOT NULL AUTO_INCREMENT,
  					zg_acc_fk int(6) 		 DEFAULT NULL,
  					zg_package_fk int(6) 	 DEFAULT NULL,
            zg_reason_tx text,
            zg_created_ts int(30)	 DEFAULT NULL,
  					zg_deleted_ts int(30) 	 DEFAULT NULL,
  					PRIMARY KEY (zg_id_pk));";
  			$zdbh->prepare($sql_make)->execute();
		}
	#now we add thier old information in case we want to reactivate them
	$sql  = "SELECT * FROM x_zgodx_lockedaccounts WHERE zg_acc_fk='".$b."' AND zg_deleted_ts IS NULL";
	$listlocked = $zdbh->query($sql)->rowCount();
		if($listlocked == 0){
			$sql="INSERT INTO x_zgodx_lockedaccounts (
							  zg_id_pk,					   
		  					zg_acc_fk,
		 					  zg_package_fk,
							  zg_reason_tx,
							  zg_created_ts) VALUES (
		 					  '',
							  '".$b."',
		 					  '".$rowac['ac_package_fk']."',
							  '".$c."',
							  ".time().")";
			$zdbh->prepare($sql)->execute();
		}
	#get last id in table packages for future use	
	$sql_last  = "SELECT pk_name_vc FROM x_packages";
	$list_last = $zdbh->query($sql_last)->rowCount();	
	$lid = $list_last+1;
	//echo $lid;
	#now we create the package if it doesnt exist
	$sql  = "SELECT pk_name_vc FROM x_packages WHERE pk_name_vc='LOCKED ACCOUNTS'";
	$listpackage = $zdbh->query($sql)->rowCount();
		if($listpackage == 0){
			$sql="INSERT INTO x_packages (
							  pk_id_pk,
							  pk_name_vc,					   
		  					pk_reseller_fk,
		 					  pk_enablephp_in,
							  pk_enablecgi_in,
							  pk_created_ts) VALUES (
							  '".$lid."',
		 					  'LOCKED ACCOUNTS',
							  '1',
		 					  '0',
							  '0',
							  ".time().")";
			$zdbh->prepare($sql)->execute();
		}
	#then we set the quota for the LOCKED ACCOUNTS package
	$sql  		 = "SELECT qt_id_pk FROM x_quotas WHERE qt_id_pk='".$lid."'";
	$listpackage = $zdbh->query($sql)->rowCount();
		if($listpackage == 0){
			$sql = "INSERT INTO x_quotas (
							  qt_id_pk,
							  qt_package_fk,					   
		  					qt_domains_in,
		 					  qt_subdomains_in,
							  qt_parkeddomains_in,
							  qt_mailboxes_in,
							  qt_fowarders_in,
							  qt_distlists_in,
							  qt_ftpaccounts_in,
							  qt_mysql_in,
							  qt_diskspace_bi,
							  qt_bandwidth_bi) VALUES (
							  '".$lid."',
		 					  '".$lid."',
							  '0',
		 					  '0',
							  '0',
							  '0',
							  '0',
		 					  '0',
							  '1',
							  '1',
							  '1',
							  '1')";
			$zdbh->prepare($sql)->execute();
		}
	#finally lets move them to the locked package
	$sql_idd  = "SELECT pk_id_pk FROM x_packages WHERE pk_name_vc='LOCKED ACCOUNTS'";
			$list_idd  = $zdbh->query($sql_idd);
			$list_idd->setFetchMode(PDO::FETCH_ASSOC);
			$row_idd   = $list_idd->fetch();
			$lidd   = $row_idd['pk_id_pk'];
	
	$sql  = "UPDATE x_accounts SET ac_package_fk='".$lidd."' WHERE ac_id_pk='".$b."'";
	$zdbh->prepare($sql)->execute();
	
	#also lets add +1 to thier usage incase they are at 0, so the progress bars work properly
	$sql  = "UPDATE x_bandwidth SET bd_transamount_bi = bd_transamount_bi +1, bd_diskamount_bi = bd_diskamount_bi +1 WHERE bd_acc_fk='".$b."' AND bd_month_in ='".date('Ym')."'";
	$zdbh->prepare($sql)->execute();
	#complete.  Account is now in the LOCKED ACCOUNT package
		
	//return shout_me('Account has been LOCKED for user: <b>'.$username.'</b><br>Settings will not take effect until the ZPanel Daemon has been ran',"success");	
	}else{
	#account already locked
	//return shout_me('Account: <b>'.$username.'</b> is already locked!',"danger");
	}
	return shout_me('Account has been LOCKED for user: <b>'.$username.'</b>. Settings will not take effect until the ZPanel Daemon has been ran<br /><a href="#" title="Run ZPanel Daemon" onclick="Popup=window.open(\'modules/zpanelconfig/code/rundaemon.php\',\'Popup\',\'toolbar=no,location=no,status=no,menubar=yes,scrollbars=yes,resizable=no, width=600,height=600,left=430,top=23\'); return false;">Run Daemon</a>',"success");	
	}

	#if there is a UNLOCK request...
	if($a == "UNLOCKACC"){
	#get account info and unapend password
	$sql  		= "SELECT ac_user_vc, ac_pass_vc, ac_package_fk FROM x_accounts WHERE ac_id_pk='".$b."'";
			$listac  = $zdbh->query($sql);
			$listac->setFetchMode(PDO::FETCH_ASSOC);
			$rowac   = $listac->fetch();
      $username   = $rowac['ac_user_vc'];
      $lockedpass = $rowac['ac_pass_vc'];
      
		if(substr($lockedpass, 0, 8) == "!!!!!!!!"){
		$lockedpass = trim(substr($lockedpass, 8, 50));
		$sql_insert  		= "UPDATE x_accounts SET ac_pass_vc ='".$lockedpass."' WHERE ac_id_pk='".$b."'";
		$zdbh->prepare($sql_insert)->execute();
		#get thier original package from the locked account databse
		$sql  		= "SELECT * FROM x_zgodx_lockedaccounts WHERE zg_acc_fk='".$b."' AND zg_deleted_ts IS NULL";
	      $listlocked = $zdbh->query($sql);
				$listlocked->setFetchMode(PDO::FETCH_ASSOC);
				$rowlocked   = $listlocked->fetch();
		$totallocked= $zdbh->query($sql)->rowCount();
			if($totallocked > 0){
			$package 	= $rowlocked['zg_package_fk'];
			#delete them from the locked account database
			$sql_locked  		= "UPDATE x_zgodx_lockedaccounts SET zg_deleted_ts ='".time()."' WHERE zg_acc_fk='".$b."'";
			$zdbh->prepare($sql_locked)->execute();
			#finally lets move them to thier original package
			$sql  		= "UPDATE x_accounts SET ac_package_fk='".$package."' WHERE ac_id_pk='".$b."'";
			//DataExchange("w",$z_db_name,$sql);
			$zdbh->prepare($sql)->execute();
			}
			#complete.		
	//	return shout_me('Account has been UN-LOCKED for user: <b>'.$username.'</b><br>Settings will not take effect until the ZPanel Daemon has been ran',"success");
		}else{
		#account wasn't locked to begin with
		//return shout_me('Account: <b>'.$username.'</b> is already unlocked!',"danger");
		}
	return shout_me('Account has been UN-LOCKED for user: <b>'.$username.'</b>. Settings will not take effect until the ZPanel Daemon has been ran. <br /><a href="#" title="Run ZPanel Daemon" onclick="Popup=window.open(\'modules/zpanelconfig/code/rundaemon.php\',\'Popup\',\'toolbar=no,location=no,status=no,menubar=yes,scrollbars=yes,resizable=no, width=600,height=600,left=430,top=23\'); return false;">Run Daemon</a>',"success");
	}
}

function ResetQuota($a, $b){
		global $zdbh;
	if($a == "RESETBW"){
	$sql  = "UPDATE x_bandwidth SET bd_transamount_bi='1' WHERE bd_acc_fk ='".$b."' AND bd_month_in='".date('Ym')."'";
	$zdbh->prepare($sql)->execute();
	$sql  = "SELECT ac_user_vc FROM x_accounts WHERE ac_id_pk='".$b."'";
	      $list = $zdbh->query($sql);
				$list->setFetchMode(PDO::FETCH_ASSOC);
				$row   = $list->fetch();
	$username = $row['ac_user_vc'];
	return shout_me('BandthWidth Quota has been reset for User Account: <b>'.$username.'</b>',"success");
	}
		
	if($a == "RESETDS"){
	$sql  = "UPDATE x_bandwidth SET bd_diskamount_bi='1' WHERE bd_acc_fk ='".$b."' AND bd_month_in='".date('Ym')."'";
	$zdbh->prepare($sql)->execute();
	$sql  = "SELECT ac_user_vc FROM x_accounts WHERE ac_id_pk='".$b."'";
	      $list = $zdbh->query($sql);
				$list->setFetchMode(PDO::FETCH_ASSOC);
				$row   = $list->fetch();
	$username = $row['ac_user_vc'];
	return shout_me('Disk Quota has been reset for User Account: <b>'.$username.'</b>',"success");
	}
} 



function xUnDelete($a, $b){
		global $zdbh;
	if($a == "VHOST"){
	$sql  = "UPDATE x_vhosts SET vh_deleted_ts=NULL WHERE vh_id_pk ='".$b."'";
	$zdbh->prepare($sql)->execute();
	$sql  = "SELECT vh_name_vc FROM x_vhosts WHERE vh_id_pk='".$b."'";
	      $list = $zdbh->query($sql);
				$list->setFetchMode(PDO::FETCH_ASSOC);
				$row   = $list->fetch();
	$name = $row['vh_name_vc'];
	return shout_me('Virtual Host: <b>'.$name.'</b> has been restored.',"success");
	}
		
	if($a == "PACKAGE"){
	$sql  = "UPDATE x_packages SET pk_deleted_ts=NULL WHERE pk_id_pk ='".$b."'";
	$zdbh->prepare($sql)->execute();
	$sql  = "SELECT pk_name_vc FROM x_packages WHERE pk_id_pk='".$b."'";
	      $list = $zdbh->query($sql);
				$list->setFetchMode(PDO::FETCH_ASSOC);
				$row   = $list->fetch();
	$name = $row['pk_name_vc'];
	return shout_me('Package: <b>'.$name.'</b> has been restored.',"success");
	}
	
	if($a == "DATABASE"){
	$sql  = "UPDATE x_mysql_databases SET my_deleted_ts=NULL WHERE my_id_pk ='".$b."'";
	$zdbh->prepare($sql)->execute();
	$sql  = "SELECT my_name_vc FROM x_mysql_databases WHERE my_id_pk='".$b."'";
	      $list = $zdbh->query($sql);
				$list->setFetchMode(PDO::FETCH_ASSOC);
				$row   = $list->fetch();
	$name = $row['my_name_vc'];
	return shout_me('Database: <b>'.$name.'</b> has been restored.',"success");
	}	
	
	if($a == "EMAILACC"){
	$sql  = "UPDATE x_mailboxes SET mb_deleted_ts=NULL WHERE mb_id_pk ='".$b."'";
	$zdbh->prepare($sql)->execute();
	$sql  = "SELECT mb_address_vc FROM x_mailboxes WHERE mb_id_pk='".$b."'";
	      $list = $zdbh->query($sql);
				$list->setFetchMode(PDO::FETCH_ASSOC);
				$row   = $list->fetch();
	$name = $row['mb_address_vc'];
	return shout_me('Mailbox: <b>'.$name.'</b> has been restored.',"success");
	}		

	if($a == "EMAILFWD"){
	$sql  = "UPDATE x_forwarders SET fw_deleted_ts=NULL WHERE fw_id_pk ='".$b."'";
	$zdbh->prepare($sql)->execute();
	$sql  = "SELECT fw_address_vc, fw_destination_vc FROM x_forwarders WHERE fw_id_pk='".$b."'";
	      $list = $zdbh->query($sql);
				$list->setFetchMode(PDO::FETCH_ASSOC);
				$row   = $list->fetch();
	$name = $row['fw_address_vc'];
	$dest = $row['fw_destination_vc'];
	return shout_me('Mailbox forwarder: <b>'.$name.' --> '.$dest.'</b> has been restored.',"success");
	}		

	if($a == "ALIAS"){
	$sql  = "UPDATE x_aliases SET al_deleted_ts=NULL WHERE al_id_pk ='".$b."'";
	$zdbh->prepare($sql)->execute();
	$sql  = "SELECT al_address_vc, al_destination_vc FROM x_aliases WHERE al_id_pk='".$b."'";
	      $list = $zdbh->query($sql);
				$list->setFetchMode(PDO::FETCH_ASSOC);
				$row   = $list->fetch();
	$name = $row['al_address_vc'];
	$dest = $row['al_destination_vc'];
	return shout_me('Mailbox Alias: <b>'.$name.' --> '.$dest.'</b> has been restored.',"success");
	}	
	
	if($a == "DISTLIST"){
	$sql  = "UPDATE x_distlists SET dl_deleted_ts=NULL WHERE dl_id_pk ='".$b."'";
	$zdbh->prepare($sql)->execute();
	$sql  = "SELECT dl_address_vc FROM x_distlists WHERE dl_id_pk='".$b."'";
	      $list = $zdbh->query($sql);
				$list->setFetchMode(PDO::FETCH_ASSOC);
				$row   = $list->fetch();
	$name = $row['dl_address_vc'];
	return shout_me('Distribution List: <b>'.$name.'</b> has been restored.',"success");
	}		

	if($a == "FTPACC"){
	$sql  = "UPDATE x_ftpaccounts SET ft_deleted_ts=NULL WHERE ft_id_pk ='".$b."'";
	$zdbh->prepare($sql)->execute();
	$sql  = "SELECT ft_user_vc FROM x_ftpaccounts WHERE ft_id_pk='".$b."'";
	      $list = $zdbh->query($sql);
				$list->setFetchMode(PDO::FETCH_ASSOC);
				$row   = $list->fetch();
	$name = $row['ft_user_vc'];
	return shout_me('FTP Account: <b>'.$name.'</b> has been restored.',"success");
	}	

	if($a == "CRON"){
	$sql  = "UPDATE x_cronjobs SET ct_deleted_ts=NULL WHERE ct_id_pk ='".$b."'";
	$zdbh->prepare($sql)->execute();
	$sql  = "SELECT ct_fullpath_vc, ct_description_tx FROM x_cronjobs WHERE ct_id_pk='".$b."'";
	      $list = $zdbh->query($sql);
				$list->setFetchMode(PDO::FETCH_ASSOC);
				$row   = $list->fetch();
	$name = $row['ct_fullpath_vc'];
	$desc = $row['ct_description_tx'];
	return shout_me('Cron Job: <b>'.$name.'<small>('.$desc.')</small></b> has been restored.',"success");
	}	
		
} 

function GetQuotaz($a, $b=0) {
		global $zdbh;
		# DESCRIPTION: Returns the current usage of a particular resource.
    # FUNCTION RELEASE: 10.0.2
    # FUNCTION AUTHOR: JK (jkmods.tk)
    $resource = $a;
    $acc_key = $b;
        if ($resource == 'diskspace_usage') {
            $sql = "SELECT bd_diskamount_bi FROM x_bandwidth WHERE bd_acc_fk=" . $acc_key . " AND bd_month_in=" . date("Ym", time()) . "";
          $queries  = $zdbh->query($sql);
          $queries->setFetchMode(PDO::FETCH_ASSOC);
          $rows   = $queries->fetch();
          $end = $rows['bd_diskamount_bi'];
            
        }
        if ($resource == 'bandwidth_usage') {
            $sql ="SELECT bd_transamount_bi FROM x_bandwidth WHERE bd_acc_fk=" . $acc_key . " AND bd_month_in=" . date("Ym", time()) . "";
            $queries  = $zdbh->query($sql);
            $queries->setFetchMode(PDO::FETCH_ASSOC);
            $rows   = $queries->fetch();
            $end = $rows['bd_transamount_bi'];
        }	
        if ($resource == 'diskspace_max') {
            $sql = "SELECT qt_diskspace_bi FROM x_quotas WHERE qt_package_fk='" .$acc_key. "'";
          $queries  = $zdbh->query($sql);
          $queries->setFetchMode(PDO::FETCH_ASSOC);
          $rows   = $queries->fetch();
          $end = $rows['qt_diskspace_bi'];
            
        }
        if ($resource == 'bandwidth_max') {
            $sql ="SELECT qt_bandwidth_bi FROM x_quotas WHERE qt_package_fk='" .$acc_key. "'";
            $queries  = $zdbh->query($sql);
            $queries->setFetchMode(PDO::FETCH_ASSOC);
            $rows   = $queries->fetch();
            $end = $rows['qt_bandwidth_bi'];
        }	
		return $end;	
  }


function ZGodXDeleteFTP($a, $n, $i, $acc) {
        global $zdbh;
        global $controller;

      if($a == "DELETEFTP"){
        $sql = $zdbh->prepare("UPDATE x_ftpaccounts SET ft_deleted_ts='".time()."' WHERE ft_id_pk='" .$i. "'");
        $sql->execute();
        if (file_exists("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetSystemOption('ftp_php') . "")) {
            include("modules/" . $controller->GetControllerRequest('URL', 'module') . "/code/" . ctrl_options::GetSystemOption('ftp_php') . "");
        }
        
        xTriggerLog($acc, $b="User FTP account ".$n." ID: " .$i. " was deleted by zGodxAdmin.");
       return shout_me('User FTP account: <b>' .$n. '</b> was deleted</b>',"success");
      } 
}



function zGodxDeleteCron($nn, $ii){
        global $zdbh;
        //runtime_csfr::Protect();
        
        //$sql = "SELECT * FROM x_cronjobs WHERE ct_acc_fk='".$nn."' AND ct_deleted_ts IS NULL";
       // $totalcrons =$zdbh->query($sql)->rowCount();
        
        //if($totalcrons == 1) {
        $sql_del = $zdbh->prepare("UPDATE x_cronjobs SET ct_deleted_ts='" .time(). "' WHERE ct_acc_fk='".$nn."' AND ct_id_pk='".$ii."' AND ct_deleted_ts IS NULL");
        $sql_del->execute();

        xTriggerLog($nn, $b="User cron job ID: " .$ii. " was deleted by zGodxAdmin.");
        return shout_me('Cron Job ID: ' .$ii. ' was <b>deleted</b>',"success");
        //} else {
        //return shout_me('Error: Cron Job ID: ' .$ii. ' <b>could not be deleted</b> TC:'. $totalcrons.'!',"danger");
        //}   
    }
    
/*    
function zGodxDeleteVhost($id, $name){
        global $zdbh;
        //runtime_csfr::Protect();
        
        //$sql = "SELECT * FROM x_vhosts WHERE vh_id_pk='".$id."' AND vh_deleted_ts IS NULL";
        //$totalhosts =$zdbh->query($sql)->rowCount();
        
        //if($totalhosts == 1) {
        $sql_del = $zdbh->prepare("UPDATE x_vhosts SET vh_deleted_ts='" .time(). "' WHERE vh_id_pk='".$id."' AND vh_deleted_ts IS NULL");
        $sql_del->execute();

        xTriggerLog('1', $b="Virtual Host: " .$name. " was deleted by zGodxAdmin.");
        return shout_me('Virtual Host: <b>' .$name. '</b> was <b>deleted</b>',"success");
        //} else {
        //return shout_me('Error: Virtual Host ID: ' .$id. ' <b>could not be deleted</b>!',"danger");
        //}   
    }    

function zGodxDeletePack($id, $name){
        global $zdbh;

        $sql_del = $zdbh->prepare("UPDATE x_packages SET pk_deleted_ts='" .time(). "' WHERE pk_id_pk='".$id."' AND pk_deleted_ts IS NULL");
        $sql_del->execute();

        xTriggerLog('1', $b="Package: " .$name. " was deleted by zGodxAdmin.");
        return shout_me('Package: <b>' .$name. '</b> was <b>deleted</b>',"success");
    }     
    */


function xShadowUser($client_id) {
        global $zdbh;
        global $controller;
        //runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        if ($currentuser['username'] == 'zadmin') {
            $sql = "SELECT * FROM x_accounts WHERE ac_deleted_ts IS NULL ORDER BY ac_user_vc";
            $numrows = $zdbh->prepare($sql);          
        } else {
            $sql = "SELECT COUNT(*) FROM x_accounts WHERE ac_reseller_fk = :userid AND ac_deleted_ts IS NULL";
            $numrows = $zdbh->prepare($sql);
            $numrows->bindParam(':userid', $currentuser['userid']);            
        }
        if ($numrows->execute()) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare($sql);
                if ($currentuser['username'] == 'zadmin') {
                    //no bind needed
                } else {
                    //bind the username
                    $sql->bindParam(':userid', $currentuser['userid']);
                }
                $sql->execute();
                while ($rowclients = $sql->fetch()) { 
                    //if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inShadow_' . $rowclients['ac_id_pk']))) {
                    if ($client_id!='') {
                        ctrl_auth::KillCookies();
                        ctrl_auth::SetSession('ruid', $currentuser['userid']);
                        ctrl_auth::SetUserSession($client_id);
                        header("location: /");
                        exit;
                    }
                }
            }
        }
   }
    
    

# MAIL ON/OFF
function zToggleMail($s, $u){
  global $zdbh;
    if ( $s == "MAILOFF"){
    $sql = "UPDATE x_mailboxes SET mb_enabled_in=0 WHERE mb_address_vc='" .$u. "'";
    $zdbh->query($sql)->execute();
    return shout_me('User mailbox <b>'.$u.'</b> has been Disabled',"success");
    }
    
    if ( $s == "MAILON") {
    $sql = "UPDATE x_mailboxes SET mb_enabled_in=1 WHERE mb_address_vc='" .$u. "'";
    $zdbh->query($sql)->execute();
    return shout_me('User mailbox <b>'.$u.'</b> has been Enabled',"success");
    }  
}


# EDIT USER DETAILS
function UserEdit($x, $f, $e, $a, $p, $ph, $i){
	global $zdbh;
	
	if($x == "EDIT"){
	$sql = "UPDATE x_profiles SET ud_fullname_vc='".$f."',
								  ud_address_tx= '".$a."',
								  ud_postcode_vc='".$p."',
								  ud_phone_vc=   '".$ph."' WHERE
								  ud_user_fk =    '".$i."'";
	
	$zdbh->prepare($sql)->execute();	
	# email is in different TABLE
	$sql = "UPDATE x_accounts SET ac_email_vc='".$e."' WHERE
								  ac_id_pk =    '".$i."'";
								  
  $zdbh->prepare($sql)->execute();	
  
	return shout_me('User information for <b>'.$f.'</b> has been updated',"success");
	}
}


# ADD REPORT TO CRON JOBS
function zGodxReportCron($a, $b) {
 #function for adding, reactivating or removing zGodx weekly reports
        global $zdbh;
 if($a=="CRON" && $b=="ON"){
    #check db
     $sql_tot = "SELECT * FROM x_cronjobs WHERE ct_acc_fk =1 AND ct_description_tx='zGodx_Daily_Reports'";
     $totalcrons =$zdbh->query($sql_tot)->rowCount();
   if($totalcrons==0) {
    #create Cron
    $script='modules/zgodx/main/crons/zGodx_DailyReport.php';
    $desc='zGodx_Daily_Reports';
    $timing='0 0 * * *';
    # full path to daily report file
    if (file_exists('/ZPanel/panel/modules/zgodx/main/crons/zGodx_DailyReport.php')){ # WIN
    $fullpath='/ZPanel/panel/modules/zgodx/main/crons/zGodx_DailyReport.php';
    }
    if (file_exists('/etc/zpanel/panel/modules/zgodx/main/crons/zGodx_DailyReport.php')){ #POSIX
    $fullpath='/etc/zpanel/panel/modules/zgodx/main/crons/zGodx_DailyReport.php';
    }
    
    $time_now = time();
    
    $sql_add ="INSERT INTO x_cronjobs (ct_acc_fk, ct_script_vc, ct_description_tx, ct_timing_vc, ct_fullpath_vc, ct_created_ts) VALUES ('1', '".$script."', '".$desc."', '".$timing."', '".$fullpath."', " . time() . ")";
    $zdbh->prepare($sql_add)->execute();
    xTriggerLog('1', $b="New cron job has been added by zGodXAdmin\rDescription: zGodx Daily Reports");
    return shout_me('Daily Reports are enabled: Cron job added to zadmin account.',"success");
   } else {
       # reactivate cron
     $sql_edit = "UPDATE x_cronjobs SET ct_deleted_ts=NULL WHERE ct_acc_fk =1 AND ct_description_tx='zGodx_Daily_Reports'";
     $zdbh->prepare($sql_edit)->execute();
     xTriggerLog('1', $b="New cron job has been added by zGodXAdmin\rDescription: zGodx Daily Reports");
     return shout_me('Daily Reports are updated: Cron job added to zadmin account.',"success");
   }
 }
  
 if($a=="CRON" && $b=="OFF"){
  #delete Cron
  $sql_del="UPDATE x_cronjobs SET ct_deleted_ts=" . time() . " WHERE ct_acc_fk =1 AND ct_description_tx='zGodx_Daily_Reports'";
  $zdbh->query($sql_del)->execute();	
  xTriggerLog('1', $b="Cron job has been removed by zGodXAdmin\rDescription: zGodx Daily Reports");
  return shout_me('Daily Reports are disabled: Cron job removed from zadmin account.',"success");
 } 

}

# GET FULL URL
function GetFullURL() {
    # DESCRIPTION: Returns the full URL of the current PHP script.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    //if ($_SERVER['HTTPS'] == 'on') {
    //    $protocol = 'https';
    //} else {
        $protocol = 'http';
    //}
    $fretval = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return $fretval;
}

# NOT NEEDED!
function GetNormalModuleURL($a) {
    # DESCRIPTION: Returns the correct Module Loader URL.
    # FUNCTION RELEASE: 5.0.0
    # FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
    //$spliton = explode("&", $a);
    //$fretval = $spliton[0] . "&" . $spliton[1];
    //return $fretval;
    return $a;
}

# WINDOWS SLASHES
function ChangeSafeSlashesToWin2($a=0){
	# DESCRIPTION: Changes MySQL safe directory slashes '\\' to Windows PHP slashes '/'.
	# FUNCTION RELEASE: 5.0.0
	# FUNCTION AUTHOR: Bobby Allen (ballen@zpanel.co.uk)
	$path = $a;
	$fretval = str_replace("/","\\",$path);
	return $fretval;
}


# GET EXACT OS

   function ShowMEOSPlatformVersion() {
        $os_abbr = strtoupper(substr(PHP_OS, 0, 3));
        if ($os_abbr == "WIN") {
            $retval = "Windows";
        } elseif ($os_abbr == "LIN") {
            $retval = "Linux";
        } elseif ($os_abbr == "FRE") {
            $retval = "FreeBSD";
        } elseif ($os_abbr == "DAR") {
            $retval = "MacOSX";
        } else {
            $retval = "Other";
        }
        return $retval;
    }

  function GetOsName() {
        preg_match_all("#(?<=\()(.*?)(?=\))#", $_SERVER['SERVER_SOFTWARE'], $osname);
        if (!empty($osname)) {
            if (strtoupper(substr($osname[0][0], 0, 3)) == "WIN") {
                $retval = "Windows";
            } else {
                $retval = $osname[0][0];
                if ($retval == "Unix") {
                    if (ShowMEOSPlatformVersion() == "MacOSX") {
                        $retval = "MacOSX";
                    }
                }
            }

        } else {
            $retval = "Unknown";
        }
        return $retval;
    }

# REPORTING LIST + MANIPULATION  
  function UpdateList($act, $adms) {
  global $zdbh;
  
  if ($act=='add_user') {

	$sql 	 	= "SHOW TABLES LIKE 'x_zgodx_reportings'";
	$listtable  =$zdbh->query($sql)->rowCount();
		if($listtable == 0){
  		   $sql_make = "CREATE TABLE IF NOT EXISTS x_zgodx_reportings (
  					zg_id_re int(6) unsigned NOT NULL AUTO_INCREMENT,
  					zg_email_re  text,
            zg_created_re int(30)	 DEFAULT NULL,
  					zg_deleted_re int(30) 	 DEFAULT NULL,
  					PRIMARY KEY (zg_id_re));";
  			$zdbh->prepare($sql_make)->execute();
		}
  
       $sqlre  = "SELECT * FROM x_zgodx_reportings WHERE zg_email_re='".$adms."' AND zg_deleted_re IS NULL";
			$totalre =$zdbh->query($sqlre)->rowCount();	
  
  if ($totalre >0 | $adms=='-select-') {
  //$k1 = 'User already in the list: '.$adms; 
  //$k2 = 'danger';  	
  } else {
			$sql = "INSERT INTO x_zgodx_reportings (
							  zg_id_re,
							  zg_email_re,					   
		  					zg_created_re) VALUES (
							  '',
		 					  '".$adms."',
							  " . time() . ")";
			$zdbh->prepare($sql)->execute();
			

	 }

	 return shout_me('User added to the list: '.$adms,'success'); 
  }
  
  if ($act=='remove_user') {

  $sql_del = "UPDATE x_zgodx_reportings SET zg_deleted_re='".time()."' WHERE zg_email_re='".$adms."'";
	$zdbh->prepare($sql_del)->execute();	
  return shout_me('User removed from the list: '.$adms,"success");
  }
}
?>