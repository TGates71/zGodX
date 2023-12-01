<?php
function zGodX_main_data(){
$end ='';


			$user_array = ctrl_users::GetUserDetail();
			$user_x  = $user_array['username'];
			$user_x_id = $user_array['userid'];
      global $zdbh;
			
if ($user_x == "zadmin" || $user_x_id ==1){ #If zadmin we get everything

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
$sql = "SELECT my_id_pk FROM x_mysql_databases WHERE my_deleted_ts IS NULL";
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

#total bandwidth
$sql = "SELECT SUM(bd_transamount_bi), bd_month_in FROM x_bandwidth WHERE bd_month_in ='".date('Ym')."'";
$listbw        = $zdbh->prepare($sql)->execute();
$listbwx       = $zdbh->query($sql);
$listbwx->setFetchMode(PDO::FETCH_ASSOC);
$rowbw         = $listbwx->fetch();
$totalabw      = $zdbh->query($sql)->rowCount();
$monthlybw     = ($rowbw['SUM(bd_transamount_bi)'] / 1000000000);
$monthlybw     = substr($monthlybw, 0, 4)." GB";

#total disk usage
$sql = "SELECT SUM(bd_diskamount_bi), bd_month_in FROM x_bandwidth WHERE bd_month_in ='".date('Ym')."'";
$listds        = $zdbh->prepare($sql)->execute();
$listdsx       = $zdbh->query($sql);
$listdsx->setFetchMode(PDO::FETCH_ASSOC);
$rowds        = $listdsx->fetch();
$totalads      = $zdbh->query($sql)->rowCount();
$monthlyds     = ($rowds['SUM(bd_diskamount_bi)'] / 1000000000);
$monthlyds     = substr($monthlyds, 0, 4)." GB";


// ------------------------------------------------------------- TODO
}else{ #Not zadmin, we only get resller account info

#total accounts
$sql = "SELECT ac_id_pk FROM x_accounts WHERE ac_reseller_fk='" .$user_x_id. "' OR ac_id_pk='" .$user_x_id. "' AND ac_deleted_ts IS NULL";
$listacounts   = $zdbh->query($sql)->execute();
$totalaccounts = $zdbh->query($sql)->rowCount() -1;
#total vhosts
$sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk='" .$user_x_id. "' OR ac_id_pk='" .$user_x_id. "' AND ac_deleted_ts IS NULL";
$list = $zdbh->query($sql)->execute();
$total = $zdbh->query($sql)->rowCount();
      $lists     = $zdbh->query($sql);
			$lists->setFetchMode(PDO::FETCH_ASSOC);
$client=array();
 if($total <> 0){
	//while($row = mysql_fetch_assoc($list)){
  while($row = $lists->fetch()){
	$client[] = $row['ac_id_pk'];
    }
 }
$sql = "SELECT vh_id_pk FROM x_vhosts WHERE vh_acc_fk IN('".join("','", $client)."') AND vh_deleted_ts IS NULL";
$listhosts = $zdbh->query($sql)->execute();
$totalhosts = $zdbh->query($sql)->rowCount();
#total packages
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
$sql = "SELECT pk_id_pk FROM x_packages WHERE pk_reseller_fk IN('".join("','", $client)."') AND pk_deleted_ts IS NULL";
$listpacks = $zdbh->query($sql)->execute();
$totalpacks  = $zdbh->query($sql)->rowCount();
#reseller info
//$sql = "SELECT rc_id_pk FROM x_resellers WHERE rc_acc_fk='" .$user_x_id. "'";
//$listrs = $zdbh->query($sql)->execute();
//$totalrs  = $zdbh->query($sql)->rowCount();
$sql = "SELECT ac_id_pk FROM x_accounts WHERE ac_group_fk=2 AND ac_deleted_ts IS NULL";
$listrs        = $zdbh->query($sql)->execute();
$totalrs       = $zdbh->query($sql)->rowCount();

#total mailboxes
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
$sql = "SELECT mb_id_pk FROM x_mailboxes WHERE mb_acc_fk IN('".join("','", $client)."') AND mb_deleted_ts IS NULL";
$listmail = $zdbh->query($sql)->execute();
$totalmail  = $zdbh->query($sql)->rowCount();
#total mysql
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
$sql = "SELECT my_id_pk FROM x_mysql_databases WHERE my_acc_fk IN('".join("','", $client)."') AND my_deleted_ts IS NULL";
$listmysql = $zdbh->query($sql)->execute();
$totalmysql  = $zdbh->query($sql)->rowCount();
#total ftp
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
$sql = "SELECT ft_id_pk FROM x_ftpaccounts WHERE ft_acc_fk IN('".join("','", $client)."') AND ft_deleted_ts IS NULL";
$listftpl= $zdbh->query($sql)->execute();
$totalftp  = $zdbh->query($sql)->rowCount();
#total cron jobs
$sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk='" .$user_x_id. "' OR ac_id_pk='" .$user_x_id. "' AND ac_deleted_ts IS NULL";
$list   = $zdbh->query($sql)->execute();
$total  = $zdbh->query($sql)->rowCount();
      $lists     = $zdbh->query($sql);
			$lists->setFetchMode(PDO::FETCH_ASSOC);
$client=array();
 if($total <> 0){
  while($row = $lists->fetch()){
	$client[] = $row['ac_id_pk'];
    }
 }
$sql = "SELECT ct_id_pk FROM x_cronjobs WHERE ct_acc_fk IN('".join("','", $client)."') AND ct_deleted_ts IS NULL";
$listcron   = $zdbh->query($sql)->execute();
$totalcron  = $zdbh->query($sql)->rowCount();
#total distrobution list users
$sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk='" .$user_x_id. "' OR ac_id_pk='" .$user_x_id. "' AND ac_deleted_ts IS NULL";
$list   = $zdbh->query($sql)->execute();
$totaldist  = $zdbh->query($sql)->rowCount();
      $lists     = $zdbh->query($sql);
			$lists->setFetchMode(PDO::FETCH_ASSOC);
$client=array();
 if($total <> 0){
  while($row = $lists->fetch()){
	$client[] = $row['ac_id_pk'];
    }
 } 
$sql = "SELECT du_id_pk FROM x_distlistusers WHERE du_deleted_ts IS NULL";
$listdist      = $zdbh->query($sql)->execute();
$totaldist     = $zdbh->query($sql)->rowCount(); 
#total mail forwards
$sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk='" .$user_x_id. "' OR ac_id_pk='" .$user_x_id. "' AND ac_deleted_ts IS NULL";
$list   = $zdbh->query($sql)->execute();
$total  = $zdbh->query($sql)->rowCount();
      $lists     = $zdbh->query($sql);
			$lists->setFetchMode(PDO::FETCH_ASSOC);
$client=array();
 if($total <> 0){
  while($row = $lists->fetch()){
	$client[] = $row['ac_id_pk'];
    }
 }
$sql = "SELECT fw_id_pk FROM x_forwarders WHERE fw_acc_fk IN('".join("','", $client)."') AND fw_deleted_ts IS NULL";
$listforwards   = $zdbh->query($sql)->execute();
$totalforwards  = $zdbh->query($sql)->rowCount();
#total aliases
$sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk='" .$user_x_id. "' OR ac_id_pk='" .$user_x_id. "' AND ac_deleted_ts IS NULL";
$list   = $zdbh->query($sql)->execute();
$total  = $zdbh->query($sql)->rowCount();
      $lists     = $zdbh->query($sql);
			$lists->setFetchMode(PDO::FETCH_ASSOC);
$client=array();
 if($total <> 0){
  while($row = $lists->fetch()){
	$client[] = $row['ac_id_pk'];
    }
 }
$sql = "SELECT al_id_pk FROM x_aliases WHERE al_acc_fk IN('".join("','", $client)."') AND al_deleted_ts IS NULL";
$listalias   = $zdbh->query($sql)->execute();
$totalalias  = $zdbh->query($sql)->rowCount();
#total bandwidth
$sql = "SELECT SUM(bd_transamount_bi), bd_month_in FROM x_bandwidth WHERE bd_month_in ='".date('Ym')."' AND bd_acc_fk='" .$user_x_id. "'";
$listbw        = $zdbh->prepare($sql)->execute();
$listbwx       = $zdbh->query($sql);
$listbwx->setFetchMode(PDO::FETCH_ASSOC);
$rowbw         = $listbwx->fetch();
$totalabw      = $zdbh->query($sql)->rowCount();
$monthlybw     = ($rowbw['SUM(bd_transamount_bi)'] / 1000000000);
$monthlybw     = substr($monthlybw, 0, 4)." GB";
#total disk usage
$sql = "SELECT SUM(bd_diskamount_bi), bd_month_in FROM x_bandwidth WHERE bd_month_in ='".date('Ym')."' AND bd_acc_fk='" .$user_x_id. "'";
$listds        = $zdbh->prepare($sql)->execute();
$listdsx       = $zdbh->query($sql);
$listdsx->setFetchMode(PDO::FETCH_ASSOC);
$rowds        = $listdsx->fetch();
$totalads      = $zdbh->query($sql)->rowCount();
$monthlyds     = ($rowds['SUM(bd_diskamount_bi)'] / 1000000000);
$monthlyds     = substr($monthlyds, 0, 4)." GB";
}


$end .= '
<em>zGodX &raquo; <b>Server Summary</b></em><span style="float:right;"><em>Server Uptime:'.sys_monitoring::ServerUptime().'</em></span>
<br><br>

<table cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #ccc">
<tr valign="top">
<td colspan="2">
</td>
</tr>


	<tr valign="top">
		<td align="left" width="350px">
        	<table class="table table-striped">
            	<tr>
                	<th colspan="2" style="border-top:0px">Hosting Totals</th>
                </tr>
                <tr>
                	<td><b>Accounts:</b></td><td>'.$totalaccounts.'</td>
                </tr>
				<tr>
                	<td><b>Virtual Hosts:</b></td><td>'.$totalhosts.'</td>
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
                	<td><b>Alias:</b></td><td>'.$totalalias.'</td>
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
			</table>
        </td>
        
        
        
        
        
        
        <td width="500px">
            <table width="499px" class="table">
            	<tr>
                	<td style="border:none">';
                	
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$end .="
    <script type=\"text/javascript\" src=\"modules/zgodx/code/cpuram-zepto.js\"></script>
    <script type=\"text/javascript\" src=\"modules/zgodx/code/cpuram-smoothie.js\"></script>
    <script type=\"text/javascript\">
    // Disable caching of AJAX responses
    $.ajaxSetup ({
    cache: false
    });
    var paused = false;
    var canVas = !!window.HTMLCanvasElement;
 
    if (canVas) {
    var cpuseries = new TimeSeries();
    var ramseries = new TimeSeries();
    };
    $(function (){
    if (canVas) {
      var cpuchart = new SmoothieChart({millisPerPixel:500,interpolation:'linear',
                                        grid:{strokeStyle:'#004200',sharpLines:true,millisPerLine:5000,verticalSections:10},labels:{disabled:true},
                                        maxValue:100,minValue:0}),
      cpucanvas = document.getElementById('cpu-chart');
      cpuchart.addTimeSeries(cpuseries, {strokeStyle:'#00ff00'});
      cpuchart.streamTo(cpucanvas, 500);

      var ramchart = new SmoothieChart({millisPerPixel:500,interpolation:'linear',
                                        grid:{strokeStyle:'#004200',sharpLines:true,millisPerLine:5000,verticalSections:10},labels:{disabled:true},
                                        maxValue:100,minValue:0}),
      ramcanvas = document.getElementById('ram-chart');
      ramchart.addTimeSeries(ramseries, {strokeStyle:'#00ff00'});
      ramchart.streamTo(ramcanvas, 500);
      cpucanvas.width = $(\"#cpulabel\").width();
      ramcanvas.width = $(\"#rampanel\").width();
      window.scrollTo(0, 1);
      $(window).resize(function() {
        cpucanvas.width = $(\"#cpulabel\").width();
        ramcanvas.width = $(\"#rampanel\").width();
      });  
    };
    });
    function togglex(){
      if (paused) {
        paused = false;
        $(\"#toggle\").html('<img src=\"modules/zgodx/images/stop.png\"> stop monitor ');
        probe();
      } else {
        paused = true;
        $(\"#toggle\").html('<img src=\"modules/zgodx/images/start.png\"> start monitor ')
      };
    }
    //Get data to draw in browser
    function probe(){
    $(\"#cpu\").load(\"modules/zgodx/code/cpuram.php?cpu=1\" , function() {
      if (canVas) { cpuseries.append(new Date().getTime(), $(\"#cpu\").html() ); };
      $(\"#ram\").load(\"modules/zgodx/code/cpuram.php\", function(){
        if (canVas) { ramseries.append(new Date().getTime(), $(\"#ram\").html() ); };
        //Call self again.
        if (!paused) { probe(); };
      });
    });
    }
    $(document).ready(function() {
      probe();
    });
    </script>
  <div id=\"host\" style=\"padding-left:10px;\">
    <span class=\"buttonx\" id=\"toggle\" onclick=\"togglex();\"><img src=\"modules/zgodx/images/stop.png\"> stop monitor </span>
    <div id=\"cpulabel\"><span><b>CPU Usage: </b></span><span id=\"cpu\">&nbsp;</span><span class=\"percent\">%</span></div>
    <div id=\"cpupanel\"><canvas id=\"cpu-chart\" width=\"490\" height=\"205\">No canvas</canvas></div>
    <br />
    <div id=\"ramlabel\"><span><b>RAM Usage: </b></span><span id=\"ram\">&nbsp;</span><span class=\"percent\">%</span></div>
    <div id=\"rampanel\"><canvas id=\"ram-chart\" width=\"490\" height=\"205\">No chart</canvas></div>
  </div>
";
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$end .='           </td>
                </tr>
            </table>        
        </td> 
        
        
        
    </tr>
</table>
<br />
<br />
                	<b>Activity for today</b>
                	
            <table class="table table-striped">
             	<tr>';
			$yesterday = time() - (1 * 24 * 60 * 60);
			$yesterday = date("Y-m-d H:i:s", $yesterday); 

      if ($user_x == "zadmin" || $user_x_id ==1){ #If zadmin we get everything
			$sql = "SELECT * FROM x_logs WHERE lg_user_fk >='2' AND lg_when_ts >= '".$yesterday."' ORDER BY lg_when_ts DESC";
			}else{ #Not zadmin, we only get resller account info
			$sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk='" .$user_x_id. "' OR ac_id_pk='" .$user_x_id. "' AND ac_deleted_ts IS NULL";
			$list      = $zdbh->prepare($sql)->execute();
      $total     = $zdbh->query($sql)->rowCount();
      $lists     = $zdbh->query($sql);
			$lists->setFetchMode(PDO::FETCH_ASSOC);
			
			$client=array();
 				if($total <> 0){
						while($row = $lists->fetch()){
					$client[] = $row['ac_id_pk'];
    				}
 			}
 			
			$sql = "SELECT * FROM x_logs WHERE lg_user_fk >='2' AND lg_when_ts >= '".$yesterday."' AND lg_user_fk IN('".join("','", $client)."') ORDER BY lg_when_ts DESC";
			} #Endif
			$list_log  = $zdbh->query($sql);
			$list_log->setFetchMode(PDO::FETCH_ASSOC);
			$row_log   = $list_log->fetch();
			$total = $zdbh->query($sql)->rowCount();

  if($total <> 0){
			do{ 

				$sql 	 = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row_log['lg_user_fk'] ."'";
				$listacc = $zdbh->query($sql);
				$listacc->setFetchMode(PDO::FETCH_ASSOC);
				$rowacc  = $listacc->fetch();
				$date = $row_log['lg_when_ts'];

				$end.='
            	<tr>
                	<td>'.$date.'</td>
                    <td><a href="javascript:void(0)"
                        onClick="document.frmMain.getMain.value=\'user\';
                        document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\';
                        document.frmMain.submit();">'.$rowacc['ac_user_vc'].'</a>:</td> 
					<td>'.$row_log['lg_detail_tx'].'</td>
                </tr>';
                

			} 
			while($row_log = $list_log->fetch());
   } else {
   $end.='
            	<tr>
                	<td>No records found</td>
              </tr>';  	
   }         
$end .='                
            </table>
            <br />
            <b>Activity for the Week <a href="#_link" title="Show this weeks activity" onclick="toggle_visibility(\'logs_lastweek\');"><img style="float:right; padding-right:5px" src="modules/zgodx/images/show.png" border="0" /></a></b>';
            
            $end.= '<div id="logs_lastweek" style="display:none;">
            <table class="table table-striped">';

			$lastweek  = time() - (7 * 24 * 60 * 60);
			$lastweek = date("Y-m-d H:i:s", $lastweek); 
			
			if ($user_x == "zadmin" || $user_x_id ==1){ #If zadmin we get everything
			$sql = "SELECT * FROM x_logs WHERE lg_user_fk >='2' AND lg_when_ts >= '".$lastweek ."' ORDER BY lg_when_ts DESC";
			}else{ #Not zadmin, we only get resller account info
			$sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk='" .$user_x_id. "' OR ac_id_pk='" .$user_x_id. "' AND ac_deleted_ts IS NULL";
			$list = $zdbh->query($sql);
			$total     = $zdbh->query($sql)->rowCount();
			$lists     = $zdbh->query($sql);
      $lists->setFetchMode(PDO::FETCH_ASSOC);
			
			$client=array();
 				if($total <> 0){
					while($row = $lists->fetch()){
					$client[] = $row['ac_id_pk'];
    				}
 			}
			$sql = "SELECT * FROM x_logs WHERE lg_user_fk >='2' AND lg_when_ts >= '".$lastweek ."' AND lg_user_fk IN('".join("','", $client)."') ORDER BY lg_when_ts DESC";
			} #Endif
			$list = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row  = $list->fetch();
			
			$total     = $zdbh->query($sql)->rowCount();
			
  if($total <> 0){
			do{ 

				$sql = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['lg_user_fk'] ."'";
				$listacc = $zdbh->query($sql);
				$listacc->setFetchMode(PDO::FETCH_ASSOC);
				$rowacc  = $listacc->fetch();
				$date = $row['lg_when_ts'];

				$end .= '
            	<tr>    
				   <td>'.$date.'</td>
                   <td><a href="javascript:void(0)"
                        onClick="document.frmMain.getMain.value=\'user\';
                        document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\';
                        document.frmMain.submit();">'.$rowacc['ac_user_vc'].'</a>: </td>
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
$end .='                
            </table>
            </div>
            <br /><br />
            <b>Activity for the Month <a href="#_link" title="Show this weeks activity" onclick="toggle_visibility(\'logs_lastmonth\');"><img style="float:right; padding-right:5px" src="modules/zgodx/images/show.png" border="0" /></a></b>';
            
            $end.='
            <div id="logs_lastmonth" style="display:none;">
            <table class="table table-striped">';

			$lastmonth = time() - (30 * 24 * 60 * 60);
      $lastmonth = date("Y-m-d H:i:s", $lastmonth); 
			
			if ($user_x == "zadmin" || $user_x_id ==1){ #If zadmin we get everything
			$sql = "SELECT * FROM x_logs WHERE lg_user_fk >='2' AND lg_when_ts >= '".$lastmonth."' ORDER BY lg_when_ts DESC";
			}else{ #Not zadmin, we only get resller account info
			$sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk='" .$user_x_id. "' OR ac_id_pk='" .$user_x_id. "' AND ac_deleted_ts IS NULL";
			$list = $zdbh->query($sql);
			$total     = $zdbh->query($sql)->rowCount();
			$lists     = $zdbh->query($sql);
      $lists->setFetchMode(PDO::FETCH_ASSOC);
			
			$client=array();
 				if($total <> 0){
					while($row = $lists->fetch()){
					$client[] = $row['ac_id_pk'];
    				}
 			}
			$sql = "SELECT * FROM x_logs WHERE lg_user_fk >='2' AND lg_when_ts >= '".$lastmonth."' AND lg_user_fk IN('".join("','", $client)."') ORDER BY lg_when_ts DESC";
			} #Endif
			$list = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row  = $list->fetch();
			
			$total     = $zdbh->query($sql)->rowCount();
   if($total <> 0){
			do{ 

				$sql = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['lg_user_fk'] ."'";
				$listacc = $zdbh->query($sql);
				$listacc->setFetchMode(PDO::FETCH_ASSOC);
				$rowacc  = $listacc->fetch();
				$date = $row['lg_when_ts'];

				$end .= '
            	<tr>    
				   <td>'.$date.'</td>
                   <td><a href="javascript:void(0)"
                        onClick="document.frmMain.getMain.value=\'user\';
                        document.frmMain.getUser.value=\''.$rowacc['ac_id_pk'].'\';
                        document.frmMain.submit();">'.$rowacc['ac_user_vc'].'</a>: </td>
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

       
$end .='                
            </table>             
            </div>                 

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
</script>
';

return $end;
}
?>