<?php
if (file_exists('/ZPanel/panel/cnf/db.php')){
include ('/ZPanel/panel/cnf/db.php');
$thpt="/ZPanel"; # Zpanel on WIN
$ex="";
}
if (file_exists('/Sentora/panel/cnf/db.php')){
include ('/Sentora/panel/cnf/db.php');
$thpt="/Sentora"; # Sentora on WIN
$ex="s";
}

if (file_exists('/etc/zpanel/panel/cnf/db.php')){
include '/etc/zpanel/panel/cnf/db.php';
$thpt="/etc/zpanel";  # Zpanel on POSIX
$ex="";
}
if (file_exists('/etc/sentora/panel/cnf/db.php')){
include '/etc/sentora/panel/cnf/db.php';
$thpt="/etc/sentora"; # Sentora on POSIX
$ex="s";
}

//echo $thpt.''.$ex;


if (file_exists($thpt.'/panel/modules/zgodx/code/functions'.$ex.'.php')){
include ($thpt.'/panel/modules/zgodx/code/functions'.$ex.'.php');
}

try {
$zdbh = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $pass);
$zdbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
echo 'ERROR: ' . $e->getMessage();
}


if (file_exists($thpt.'/panel/cnf/db.php')){
include ($thpt.'/panel/cnf/db.php');
include ($thpt.'/panel/dryden/db/driver.class.php');
include ($thpt.'/panel/dryden/debug/logger.class.php');
include ($thpt.'/panel/dryden/runtime/dataobject.class.php');
include ($thpt.'/panel/dryden/sys/versions.class.php');
include ($thpt.'/panel/dryden/ctrl/options.class.php');
include ($thpt.'/panel/dryden/ctrl/auth.class.php');
include ($thpt.'/panel/dryden/ctrl/users.class.php');
include ($thpt.'/panel/dryden/fs/director.class.php');
include ($thpt.'/panel/inc/dbc.inc.php');
include ($thpt.'/panel/etc/lib/PHPMailer/class.phpmailer.php');
}

#get zadmin email*/
$sql = "SELECT ac_email_vc FROM x_accounts WHERE ac_id_pk = 1";
	      $listadminemail = $zdbh->query($sql);
				$listadminemail->setFetchMode(PDO::FETCH_ASSOC);
				$rowadminemail   = $listadminemail->fetch();
$adminemail     = $rowadminemail['ac_email_vc'];

#get home domain - using domain from x_settings -> From Address
$sql_dom = "SELECT so_value_tx FROM x_settings WHERE so_cleanname_vc = 'From Address'";
	      $list_dom = $zdbh->query($sql_dom);
				$list_dom->setFetchMode(PDO::FETCH_ASSOC);
				$row_dom   = $list_dom->fetch();
$this_host_q     = $row_dom['so_value_tx'];
$this_host_ex = explode('@',$this_host_q );
$this_host = $this_host_ex[1];


//echo 'th: '.$this_host.'<br />';

//finished with cron speciffic variables, on the the report...

#total accounts
$sql = "SELECT ac_id_pk FROM x_accounts WHERE ac_deleted_ts IS NULL";
$listacounts   = $zdbh->query($sql)->execute();
$totalaccounts = $zdbh->query($sql)->rowCount() -1;

#total vhosts
$sql = "SELECT vh_id_pk FROM x_vhosts WHERE vh_deleted_ts IS NULL";
$listhosts    = $zdbh->query($sql)->execute();
$totalhosts   = $zdbh->query($sql)->rowCount();

#total packages
$sql = "SELECT pk_id_pk FROM x_packages WHERE pk_deleted_ts IS NULL";
$listpacks    = $zdbh->query($sql)->execute();
$totalpacks   = $zdbh->query($sql)->rowCount();

#total resellers
$sql = "SELECT ac_id_pk FROM x_accounts WHERE ac_group_fk=2 AND ac_deleted_ts IS NULL";
$listrs        = $zdbh->query($sql)->execute();
$totalrs       = $zdbh->query($sql)->rowCount();

#total mailboxes
$sql = "SELECT mb_id_pk FROM x_mailboxes WHERE mb_deleted_ts IS NULL";
$listmail      = $zdbh->query($sql)->execute();
$totalmail     = $zdbh->query($sql)->rowCount();

#total mysql
$sql = "SELECT my_id_pk FROM  x_mysql_databases WHERE my_deleted_ts IS NULL";
$listmysql     = $zdbh->query($sql)->execute();
$totalmysql    = $zdbh->query($sql)->rowCount();

#total ftp
$sql = "SELECT ft_id_pk FROM x_ftpaccounts WHERE ft_deleted_ts IS NULL";
$listftpl      = $zdbh->query($sql)->execute();
$totalftp      = $zdbh->query($sql)->rowCount();

#total cron jobs
$sql = "SELECT ct_id_pk FROM x_cronjobs WHERE ct_deleted_ts IS NULL";
$listcron      = $zdbh->query($sql)->execute();
$totalcron     = $zdbh->query($sql)->rowCount();

#total distrobution list users
$sql = "SELECT du_id_pk FROM x_distlistusers WHERE du_deleted_ts IS NULL";
$listdist      = $zdbh->query($sql)->execute();
$totaldist     = $zdbh->query($sql)->rowCount();

#total mail forwards
$sql = "SELECT fw_id_pk FROM x_forwarders WHERE fw_deleted_ts IS NULL";
$listforwards  = $zdbh->query($sql)->execute();
$totalforwards = $zdbh->query($sql)->rowCount();

#total aliases
$sql = "SELECT al_id_pk FROM x_aliases WHERE al_deleted_ts IS NULL";
$listalias     = $zdbh->query($sql)->execute();
$totalalias    = $zdbh->query($sql)->rowCount();

$dateYM =date('Ym');
//echo $dateYM;

#total bandwidth
$sql = "SELECT SUM(bd_transamount_bi), bd_month_in, bd_acc_fk FROM x_bandwidth WHERE bd_month_in ='".date('Ym')."'";
			$listbw  = $zdbh->query($sql);
			$listbw->setFetchMode(PDO::FETCH_ASSOC);
			$rowbw   = $listbw->fetch();
$totalabw      = $zdbh->query($sql)->rowCount();
$monthlybw     = ($rowbw['SUM(bd_transamount_bi)'] / 1000000000);
$monthlybw     = substr($monthlybw, 0, 4)." GB";

#total disk usage
$sql = "SELECT SUM(bd_diskamount_bi), bd_month_in, bd_acc_fk FROM x_bandwidth WHERE bd_month_in ='".date('Ym')."'";
$listdsx       = $zdbh->query($sql);
$listdsx->setFetchMode(PDO::FETCH_ASSOC);
$rowds        = $listdsx->fetch();
$totalads      = $zdbh->query($sql)->rowCount();
$monthlyds     = ($rowds['SUM(bd_diskamount_bi)'] / 1000000000);
$monthlyds     = substr($monthlyds, 0, 4)." GB";

$message='
<html>
<head>
<title>zGodX Daily Report</title>
</head>
<body>
<style type="text/css">
body {
	font-size: 10px;
	font-family: \'Lucida Grande\', Verdana, Arial, Sans-Serif;
	background-color: #FFF;
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.zgrid{
	background-color: #FFF;
	border:2px solid #6593CF;
}
.zgrid td{
	background-color: #F1F1F1;
	font-size: 14px;
}
.zgrid th{
	background-color: #FFE799;
	font-weight:bold;
	text-align: left;
	font-size: 14px;
}
em {
font-size: 16px;
}
#progress {
    width: 120px;
}
.graph {
    width: 120px;
    height: 15px;
    background: rgb(168,168,168);
    position: relative;
    font-size:10px;
}
#barb {
    height: 14px;
    background: rgb(0,162,232);
    color:#fff;
}
#barg {
    height: 14px;
    background: rgb(0,128,0);
    color:#fff;
}
#baro {
    height: 14px;
    background: rgb(255,128,0);
    color:#fff;
}
#barr {
    height: 14px;
    background: rgb(128,0,0);
    color:#fff;
}
#barb p { position: absolute; text-align: center; width: 100%; margin: 0; line-height: 15px; }
#barg p { position: absolute; text-align: center; width: 100%; margin: 0; line-height: 15px; }
#baro p { position: absolute; text-align: center; width: 100%; margin: 0; line-height: 15px; }
#barr p { position: absolute; text-align: center; width: 100%; margin: 0; line-height: 15px; }
</style>

<h2>Daily zGodX Report</h2>
<small>'.date('M d, Y h:m').' - generated by zGodxAdmin</small><br /><br />
<table border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td align="left">
        	<table class="zgrid" width="100%">
            	<tr>
                	<th colspan="2">Hosting Totals</th>
                </tr>
                <tr>
                	<td><b>Accounts:</b></td><td>'.$totalaccounts.'</td>
                </tr>
				<tr>
                	<td><b>VHosts:</b></td><td>'.$totalhosts.'</td>
				</tr>
				<tr>
                	<td><b>Packages:</b></td><td>'.$totalpacks.'</td>
				</tr>
				<tr>
                	<td><b>Resellers:</b></td><td>'.$totalrs.'</td>
				</tr>
				<tr>
                	<td><b>MailBoxes:</b></td><td>'.$totalmail.'</td>
				</tr>
				<tr>
                	<td><b>MySql:</b></td><td>'.$totalmysql.'</td>
				</tr>
				<tr>
                	<td><b>FTP:</b></td><td>'.$totalftp.'</td>
				</tr>
				<tr>
                	<td><b>Cron jobs:</b></td><td>'.$totalcron.'</td>
				</tr>
				<tr>
                	<td><b>Dist Lists:</b></td><td>'.$totaldist.'</td>
				</tr>
				<tr>
                	<td><b>Mail FW:</b></td><td>'.$totalforwards.'</td>
				</tr>
				<tr>
                	<td><b>Bandwidth:</b></td><td>'.$monthlybw.'</td>
				</tr>
				<tr>
                	<td><b>Disk Usage:</b></td><td>'.$monthlyds.'</td>
				</tr>
				<tr>
                	<td><b>Alias:</b></td><td>'.$totalalias.'</td>
				</tr>
			</table>
        </td>
        <td>
    </tr>
</table>
<br />
<br /><em>zGodX &raquo; <b>Activity Report</b></em><br /><br />
<table border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td align="left">
            <table class="zgrid" width="100%">
             	<tr>
                	<th colspan="3">Activity for today</th>
                </tr>';
		
			$yesterday = time() - (1 * 24 * 60 * 60);
			$yesterday = date("Y-m-d H:i:s", $yesterday);
			$sql 	   = "SELECT * FROM x_logs WHERE lg_user_fk >='2' AND lg_when_ts >= '".$yesterday."' ORDER BY lg_when_ts DESC";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row = $list->fetch();
			$total = $zdbh->query($sql)->rowCount();
			
			if ($total >=1) {

			do{ 

				$sql 	 = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='".$row['lg_user_fk']."'";
				$listacc = $zdbh->query($sql);
				$listacc->setFetchMode(PDO::FETCH_ASSOC);
				$rowacc  = $listacc->fetch();
				$date = $row['lg_when_ts'];
				
 $message .='   <tr>
                	<td>'. $date.'</td>
                    <td>'. $rowacc['ac_user_vc'].'</a>:</td> 
                    <td>'. $row['lg_detail_tx'].'</td>
                </tr>';
                
			 } while($row = $list->fetch());        
   } else {
   
 $message .='<tr><td>No activity for today</td></tr>';   
   }
                
 $message .='           </table>';

 #get all accounts
$sql = "SELECT * FROM x_accounts WHERE ac_deleted_ts IS NULL ORDER BY ac_user_vc ASC";
			$listac  = $zdbh->query($sql);
			$listac->setFetchMode(PDO::FETCH_ASSOC);
			$rowac   = $listac->fetch();
			$totalac = $zdbh->query($sql)->rowCount();

$dateYM =date('Ym');			
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

if ($totalac > 0 ){

$message .='
<br><em>zGodX &raquo; <b>Server Bandwidth</b></em><br><br>
<table class="zgrid" cellpadding="2" cellspacing="2">
	<tr>
		<th>Server Bandwidth for '.date('M').'</th><th>Server Bandwidth for '.date('Y').'</th><th>Total Server Bandwidth</th>
    </tr>
    <tr>
        <td><b>'.FormatFileSize($rowmd['asum']).'</b></td>
        <td><b>'.FormatFileSize($rowyd['asum']).'</b></td>
        <td><b>'.FormatFileSize($rowtd['asum']).'</b></td>
    </tr>
</table>
<br>
<table border="0" cellpadding="0" cellspacing="0" width="">
	<tr>
		<td align="left">
        	<table class="zgrid" width="100%">
            	<tr>
                	<th colspan="1">Account</th>
                    <th colspan="4"><center>BW Quota Usage</center></th>
                    <th colspan="1">%Server Usage</th>
                </tr>';
            
    do{
				if ($rowac['ac_id_pk'] != 1){

	$sql = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$rowbw['bd_acc_fk']."'";
			$listacc  = $zdbh->query($sql);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch();

$BU = FormatFileSize((GetQuotaz('bandwidth_usage', $rowac['ac_id_pk'])));
$BM = FormatFileSize((GetQuotaz('bandwidth_max', $rowac['ac_package_fk'])));

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

$message .=		'<tr>
    				<td>'.$rowac['ac_user_vc'].'</td>
                    <td>'.$BU.'</td>
                    <td> <b>of</b> </td>
					<td>'.$BM.'</td>
                    <td><div id="progress" class="graph"><div id="bar'.$dcu.'" style="width:'.$maxu.'"><p>'.$maxu.'</p></div></div></td>    
                    <td><div id="progress" class="graph"><div id="bar'.$dcs.'" style="width:'.$maxs.'"><p>'.$maxs.'</p></div></div></td>    
    			</tr>';
    		}
      } while($rowac = $listac->fetch());    
$message .= '</table>
        </td>
    </tr>
</table>';

} else { $message .= '<h2>It seems you have no used bandwidth at all... Internet problems?...</h2>'; }

#get all accounts
$sql = "SELECT * FROM x_accounts WHERE ac_deleted_ts IS NULL ORDER BY ac_user_vc ASC";
			$listac  = $zdbh->query($sql);
			$listac->setFetchMode(PDO::FETCH_ASSOC);
			$rowac   = $listac->fetch();
			$totalac =$zdbh->query($sql)->rowCount();
#get server hd space
 if (zGodxShowOSVer() == "Windows") { #WINDOWS
define("DISK","c:/");
}else{ #POSIX
define("DISK","/");
} #ENDIF
$total = disk_total_space(DISK);
#get server free space
$free = disk_free_space(DISK);
#get server used space
$used = $total - $free;
if ($totalac > 0 ){	

if($total>0) { $mut = round(($used/$total)*100); } else {$mut=0;}
if($mut>0 && $mut<=74) { $dcm = 'g';} elseif($mut>74 && $mut<=90) { $dcm='o'; } elseif($mut>90 && $mut<=100) {$dcm='r';} else { $dcm='b';}
if($total>0) { $mut = $mut.'%'; } else { $mut='U/L';}	

$message .='
<br><em>zGodX &raquo; <b>Server Disk Usage</b></em><br><br>
<table class="zgrid" border="0" cellpadding="2" cellspacing="2">
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
<table border="0" cellpadding="0" cellspacing="0" width="">
	<tr>
		<td align="left">
        	<table class="zgrid" width="100%">
            	<tr>
                	<th colspan="1">Account</th>
                    <th colspan="4"><center>HDD Quota Usage</center></th>
                    <th colspan="1">%Server Usage</th>
                </tr>';
    do{
				if ($rowac['ac_id_pk'] != 1){
    
	$sql = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='".$rowbw['bd_acc_fk']."'";
			$listacc  = $zdbh->query($sql);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch();
		

$pdu = GetQuotaz('diskspace_usage', $rowac['ac_id_pk']);
$pdt = GetQuotaz('diskspace_max', $rowac['ac_package_fk']);
if($pdt>0) { $maxdu = round(($pdu/$pdt)*100); } else {$maxdu=0;}
if($maxdu>0 && $maxdu<=74) { $dcdu = 'g';} elseif($maxdu>74 && $maxdu<=90) { $dcdu='o'; } elseif($maxdu>90 && $maxdu<=100) {$dcdu='r';} else { $dcdu='b';}
if($pdt>0) { $maxdu = $maxdu.'%'; } else { $maxdu='U/L';}

$sdu = GetQuotaz('diskspace_usage', $rowac['ac_id_pk']);
$sdt = $total;
if($sdt>0) { $maxds = round(($sdu/$sdt)*100); } else {$maxds=0;}
if($maxds>0 && $maxds<=74) { $dcds = 'g';} elseif($maxds>74 && $maxds<=90) { $dcds='o'; } elseif($maxds>90 && $maxds<=100) {$dcds='r';} else { $dcds='b';}
if($sdt>0) { $maxds = $maxds.'%'; } else { $maxds='U/L';}

$message .='
    			<tr>
    				<td>'.$rowac['ac_user_vc'].'</td>
                    <td>'.FormatFileSize(GetQuotaz('diskspace_usage', $rowac['ac_id_pk'])).'</td>
                    <td><b>of</b></td>
					<td>'.FormatFileSize(GetQuotaz('diskspace_max', $rowac['ac_package_fk'])).'</td>
                    <td><div id="progress" class="graph"><div id="bar'.$dcdu.'" style="width:'.$maxdu.'"><p>'.$maxdu.'</p></div></div></td>    
                    <td><div id="progress" class="graph"><div id="bar'.$dcds.'" style="width:'.$maxds.'"><p>'.$maxds.'</p></div></div></td>    
    			</tr>';
     		}
      } while($rowac = $listac->fetch()); 

$message .='
			</table>
        </td>
    </tr>
</table>';

} else { $message .='<h2>It seems you have no used no disk space at all... Strange...</h2>'; }		

#get accounts
$sql = "SELECT * FROM x_accounts WHERE ac_deleted_ts IS NULL ORDER BY ac_user_vc ASC";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
			$total =$zdbh->query($sql)->rowCount();
if ($total > 0 ){

$message .='
<br><em>zGodX &raquo; <b>User Accounts</b></em><br><br>
<table border="0" cellpadding="0" cellspacing="0" width="">
	<tr>
		<td align="left">
        	<table class="zgrid" width="100%">
            	<tr>
                	<th colspan="1">Account</th>
                    <th colspan="1">Package</th>
                    <th colspan="1">Reseller</th>
                    <th colspan="1">BW</th>
                    <th colspan="1">DU</th>
                    <th colspan="3"><center>Status</center></th>
                    <th colspan="1">Created</th>
                </tr>';
     do{
    			if ($row['ac_id_pk'] != 1){
 $message .='   <tr>
    				<td>'.$row['ac_user_vc'].'</td>';
                    
				$sql = "SELECT pk_name_vc FROM x_packages WHERE pk_id_pk = '".$row['ac_package_fk']."'";
			$listp  = $zdbh->query($sql);
			$listp->setFetchMode(PDO::FETCH_ASSOC);
			$rowp   = $listp->fetch();
					if($rowp['pk_name_vc'] == "LOCKED ACCOUNTS"){
		   			$pname = "<font color=\"red\">".$rowp['pk_name_vc']."</font>";
					}else{
		  			 $pname = $rowp['pk_name_vc'];
					}
					
 $message .='       <td>'.$pname.'</td> '; 

				$sql = "SELECT ac_user_vc, ac_pass_vc FROM x_accounts WHERE ac_reseller_fk = '".$row['ac_reseller_fk']."'";
			$listres  = $zdbh->query($sql);
			$listres->setFetchMode(PDO::FETCH_ASSOC);
			$rowres   = $listres->fetch();

$pdua = GetQuotaz('bandwidth_usage', $row['ac_id_pk']);
$pdta = GetQuotaz('bandwidth_max', $row['ac_package_fk']);
if($pdta>0) { $maxdua = round(($pdua/$pdta)*100);} else {$maxdua =0;}
if($maxdua>0 && $maxdua<=74) { $dcdua = 'g';} elseif($maxdua>74 && $maxdua<=90) { $dcdua='o'; } elseif($maxdua>90 && $maxdua<=100) {$dcdua='r';} else { $dcdua='b';}
if($pdta>0) { $maxdua = $maxdua.'%'; } else { $maxdua='U/L';}

$sdua = GetQuotaz('diskspace_usage', $row['ac_id_pk']);
$sdta = GetQuotaz('diskspace_max', $row['ac_package_fk']);
if($sdta>0) { $maxdsa = round(($sdua/$sdta)*100); } else {$maxdsa =0;}
if($maxdsa>0 && $maxdsa<=74) { $dcdsa = 'g';} elseif($maxdsa>74 && $maxdsa<=90) { $dcdsa='o'; } elseif($maxdsa>90 && $maxdsa<=100) {$dcdsa='r';} else { $dcdsa='b';}
if($sdta>0) { $maxdsa = $maxdsa.'%'; } else { $maxdsa='U/L';}

                
$message .='     <td>'.$rowres['ac_user_vc'].'</td>
                 <td><div id="progress" class="graph"><div id="bar'.$dcdua.'" style="width:'.$maxdua.'"><p>'.$maxdua.'</p></div></div></td>    
                 <td><div id="progress" class="graph"><div id="bar'.$dcdsa.'" style="width:'.$maxdsa.'"><p>'.$maxdsa.'</p></div></div></td>';
                    
                 	if(substr($row['ac_pass_vc'], 0, 8) == '!!!!!!!!'){
					$status="<font color=\"red\">LOCKED</font>";
					}else{
					$status="<font color=\"green\">OK</font>";}

$message .='        <td>'.$status.'</td>
                    <td></td><td></td>';
				 	
                    
$message .='     <td>'.date("M d, Y",$row['ac_created_ts']).'</td>
                 </tr>';
     		 	}
      } while($row = $list->fetch());
$message .='
			</table>
        </td>
    </tr>
</table>';

} else { $message .='<h2>You have no accounts at this time... I don\'t think you should ever see this.</h2>'; } 

#get vhosts
$sql = "SELECT * FROM x_vhosts WHERE vh_deleted_ts IS NULL ORDER BY vh_acc_fk ASC";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
			$total =$zdbh->query($sql)->rowCount();
if ($total > 0){

$message .='
<br><em>zGodX &raquo; <b>Virtual Hosts</b></em><br><br>
<table border="0" cellpadding="0" cellspacing="0" width="">
	<tr>
		<td align="left">
        	<table class="zgrid" width="100%">
            	<tr>
                	<th colspan="1">Account</th>
                    <th colspan="1">Domain</th>
                    <th colspan="1">Directory</th>
                    <th colspan="1">Type</th>
                    <th colspan="1">Status</th>
                    <th colspan="1">Created</th>
                </tr>';
    do{
    
	$sql = "SELECT * FROM x_accounts WHERE ac_id_pk ='" .$row['vh_acc_fk']."'";
			$listacc  = $zdbh->query($sql);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch();
	
  $quband  = GetQuotaz('bandwidth_usage', $rowacc['ac_id_pk']);  //53692691
	$qmband  = GetQuotaz('bandwidth_max', $rowacc['ac_package_fk']); // -1
	//echo "ac_id_pk: ".$rowacc['ac_id_pk']." - quband:".$quband.' - qmband:'.$qmband."<br />";
$message .='
     			<tr>
    				<td>'.$rowacc['ac_user_vc'].'</td>
      				<td><a href="http://'.$row['vh_name_vc'].'" target="_blank">'.$row['vh_name_vc'].'</td>
                    <td>'.$row['vh_directory_vc'].'</td>';
                    
              if ($row['vh_type_in'] == 1){
							$type="<center><b>D</b></center>";
							} elseif ($row['vh_type_in'] == 2){
							$type="<center>S</center>";
							}else{
							$type="<center><i>P</i></center>";
							}
                    
$message .='                   <td>'.$type.'</td>';
                    
					if ($quband > $qmband && $qmband!="0"){    // $qmband=="0" unlimited
								$status="<font color=\"red\">LOCKED</font>";
							}else{
								if ($row['vh_active_in'] == 1){
								$status="<font color=\"green\">Live</font>";
								}else{
								$status="<font color=\"orange\">Pending</font>";
								} 
							}
                            
$message .='        <td>'.$status.'</td>
      				<td>'.date("M d, Y",$row['vh_created_ts']).'</td>
    			</tr>';
      } while($row = $list->fetch());   
$message .='
			</table>
        </td>
    </tr>
</table>';

} else { $message .='<h2>You have no domains at this time</h2>'; }	
		                 
$message .='
      </td>
    </tr>
<br /><br />    
</table>
</body>
</html>';

#add to log
$teraz = date('Y-m-d H:i:s',time());
xTriggerLog("1","Email report has been sent to zadmin by zGodXAdmin module on: ".$teraz);
echo "<br />Email has been sent to zadmin by zGodXAdmin on: ".$teraz;





#all done, lets send that puppy!
// email class
class sys_emailx extends PHPMailer {
    /**
     * Sends the email with the contents of the object (Body etc. set using the parant calls in phpMailer!)
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @return boolean 
     */
    public function SendEmailx() {
        $this->Mailer = ctrl_options::GetSystemOption('mailer_type');
        $this->From = ctrl_options::GetSystemOption('email_from_address');
        $this->FromName = ctrl_options::GetSystemOption('email_from_name');
        if (ctrl_options::GetSystemOption('email_smtp') <> 'false') {
            $this->IsSMTP();
            if (ctrl_options::GetSystemOption('smtp_auth') <> 'false') {
                $this->SMTPAuth = true;
                $this->Username = ctrl_options::GetSystemOption('smtp_username');
                $this->Password = ctrl_options::GetSystemOption('smtp_password');
            }
            if (ctrl_options::GetSystemOption('smtp_secure') <> 'false') {
                $this->SMTPSecure = ctrl_options::GetSystemOption('smtp_secure');
            }
            $this->Host = ctrl_options::GetSystemOption('smtp_server');
            $this->Port = ctrl_options::GetSystemOption('smtp_port');
        }

        ob_start();
        $send_resault = $this->Send();
        $error = ob_get_contents();
        ob_clean();
        if ($send_resault) {
            return true;
        } else {
            return false;
        }
    }

}

// email class

$emailsubject = "zGodX Daily Report - ".date('M d, Y');

  $phpmailerx = new sys_emailx();
  $phpmailerx->IsHTML(true);
  $phpmailerx->Subject = $emailsubject;
  $phpmailerx->Body = $message;
  //$phpmailerx->From = $emailfrom; - not needed
  $phpmailerx->AddAddress($adminemail); 


        echo "<br />Daily Report send to: <br />";
        echo $adminemail.'<br />';

              $sql 	 	= "SHOW TABLES LIKE 'x_zgodx_reportings'";
            $listtable  =$zdbh->query($sql)->rowCount();
            if($listtable > 0){ 
              $sqlre  = "SELECT zg_email_re, zg_created_re  FROM x_zgodx_reportings WHERE zg_deleted_re IS NULL";
              $listre  = $zdbh->query($sqlre);
              $totalre =$zdbh->query($sqlre)->rowCount();	
              $listre->setFetchMode(PDO::FETCH_ASSOC);
              $rowre   = $listre->fetch();

            if($totalre>0) {
              do {
  $phpmailerx->AddAddress($rowre['zg_email_re']);
        echo $rowre['zg_email_re'].'<br />';
                  } while($rowre = $listre->fetch());
                } 
              }
                   
  $phpmailerx->SendEmailx();
//echo "<br /><hr><br />".$message;

function zGodxShowOSVer() {
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


?>