<?php
function zGodX_server_data() {

$lastruntime = xgetLastRunTime();
$nextruntime = xgetNextRunTime();

$daemon_title="Last run: ".$lastruntime."\nNext run: ".$nextruntime;

      $end ='';

			$user_array = ctrl_users::GetUserDetail();
			$user_x  = $user_array['username'];
			$user_x_id = $user_array['userid'];
      global $zdbh;

		#if the user has ask for a service start stop or restart
		if (isset($_POST['getService'])){

			#is it apache they want to retart?
			if ($_POST['getService'] == "RESTARTAPACHE"){
				#if so then set the time in a post so that if he tries to refresh
				#the broswer, form postdata won't restart the service again
				$time = $_POST['getTime'];
					#if we have waited more than 15 seconds then do nothing on refresh
					if(($time+15) <= time()) {
					}else{
					#if we just clicked it then restart the service
					$end .= ToggleService($_POST['getService']);
					//shout_me($end,"zannounceerror", $id = "zannounce");	
					}
			#any other service we don't care, just restart it
			}else{
			$end .= ToggleService($_POST['getService']);
			}

		}

#we get the status of the services running
if (sys_monitoring::PortStatus(21)==1) {$fstatus = 1; } else {$fstatus = 0; }
if (sys_monitoring::PortStatus(25)==1) {$hstatus = 1; } else {$hstatus = 0; }
/* MySQL has to be on-line as you are viewing this page, we made this 'static' to save on port queries (saves time) amongst other reasons.
if (sys_monitoring::PortStatus(3306)==1) {$mstatus = 1; } else {$mstatus = 0; }
*/ $mstatus = 1;
if (sys_monitoring::PortStatus(80)==1) {$astatus = 1; } else {$astatus = 0; }

if ($fstatus == 1){
	$fstatus  ="<font color=\"green\">OK</font>";
	$flink    ='&nbsp;<a href="javascript:void(0)"
    		   onClick="javascript:toggle(); document.frmService.getService.value=\'STOPFTP\';
               document.frmService.submit();"
               title="Stop FTP Server"><img src="modules/zgodx/images/stop.png" border="0" /></a>';
	}else{
	$fstatus  ="<font color=\"red\">OFF</font>";
	$flink    ='&nbsp;<a href="javascript:void(0)"
                onClick="javascript:toggle(); document.frmService.getService.value=\'STARTFTP\';
                document.frmService.submit();"
                title="Start FTP Server"><img src="modules/zgodx/images/start.png" border="0" /></a>';
}

if ($hstatus == 1){
	$hstatus  ="<font color=\"green\">OK</font>";
	$hlink    ='&nbsp;<a href="javascript:void(0)"
    		   onClick="javascript:toggle(); document.frmService.getService.value=\'STOPHMAIL\';
               document.frmService.submit();"
               title="Stop Mail Server"><img src="modules/zgodx/images/stop.png" border="0" /></a>';
	}else{
	$hstatus  ="<font color=\"red\">OFF</font>";
	$hlink    ='&nbsp;<a href="javascript:void(0)"
                onClick="javascript:toggle(); document.frmService.getService.value=\'STARTHMAIL\';
                document.frmService.submit();"
                title="Start Mail Server"><img src="modules/zgodx/images/start.png" border="0" /></a>';
}

if ($mstatus == 1){
	$mstatus  ="<font color=\"green\">OK</font>";
	}else{
	$mstatus  ="<font color=\"red\">OFF</font>";
}

if ($astatus == 1){
	$astatus  ="<font color=\"green\">OK</font>";
	}else{
	$astatus  ="<font color=\"red\">OFF</font>";
}

$end .='
<table class="zgrid" width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><th colspan="5">Services Control</th></tr>
<tr>

<td>
<table class="zgrid" border="0" cellpadding="0" cellspacing="0">
	<tr>
    	<td align="left"><img src="modules/zgodx/images/zdaemon.png" title="'.$daemon_title.'"/></td>
        <td>	
            <table class="zgrid" border="0" cellpadding="0" cellspacing="0" width="100%">
               <!-- <tr>
                    <td align="center">&nbsp;<b>zDaemon</b></td>
                </tr> -->
                <tr>
                	<td align="center">
                  &nbsp;<a href="javascript:void(0)"
                    	onClick="javascript:toggle();
                        		 document.frmService.getService.value=\'zDAEMON\';
                        		 document.frmService.submit();"
                        title="Run zDeamon now!"><img src="modules/zgodx/images/go.png" border="0"/></a><br />
                        &nbsp;<a href="#" id="pop_log" title="Show zDeamon LOG"><img src="modules/zgodx/images/log.png" border="0"/></a>
                  
                  </td>
                </tr>
            </table>
         </td>
    </tr>
</table>
</td>

<td>








<table class="zgrid" border="0" cellpadding="0" cellspacing="0">
	<tr>
    	<td align="left">';
    	

		if (sys_versions::ShowOSPlatformVersion() == "Windows") {
		$end .='<img src="modules/zgodx/images/filezilla.png" />';
		}else{
		$end .='<img src="modules/zgodx/images/ftp.png" />';
		}

$end .='		
		</td>
        <td>	
            <table class="zgrid" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="center" colspan="2">'.$fstatus.'</td>
                </tr>
                <tr>
                	<td align="center">'.$flink.'</a>
                    </td>
                    <td>&nbsp;<a href="javascript:void(0)"
                    	onClick="javascript:toggle();
                        		 document.frmService.getService.value=\'RESTARTFTP\';
                        		 document.frmService.submit();"
                        title="Restart FTP Server"><img src="modules/zgodx/images/restart.png" border="0" /></a>
                    </td>
                </tr>
            </table>
         </td>
    </tr>
</table>

</td><td>

<table class="zgrid" border="0" cellpadding="0" cellspacing="0">
	<tr>
    	<td align="left">';
		
		if (sys_versions::ShowOSPlatformVersion() == "Windows") {
		$end .='<img src="modules/zgodx/images/hmail.png" />';
		}else{
		$end .='<img src="modules/zgodx/images/mail.png" />';
		}
$end .= '
		</td>
        <td>	
            <table class="zgrid" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="center" colspan="2">'. $hstatus.'</td>
                </tr>
                <tr>
                	<td align="center">'.$hlink.'</a>
                    </td>
                    <td>&nbsp;<a href="javascript:void(0)"
                    	onClick="javascript:toggle();
                        		 document.frmService.getService.value=\'RESTARTHMAIL\';
                        		 document.frmService.submit();"
                        title="Restart Mail Server"><img src="modules/zgodx/images/restart.png" border="0" /></a>
                    </td>
                </tr>
            </table>
         </td>
    </tr>
</table>

</td><td>

<table class="zgrid" border="0" cellpadding="0" cellspacing="0">
	<tr>
    	<td align="left"><img src="modules/zgodx/images/mysql.png" /></td>
        <td>	
            <table class="zgrid" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="center">'.$mstatus.'</td>
                </tr>
                <tr>
                    <td>&nbsp;<a href="javascript:void(0)"
                    	onClick="javascript:toggle();
                        		 document.frmService.getService.value=\'RESTARTMYSQL\';
                        		 document.frmService.submit();"
                        title="Restart MySQL Service"><img src="modules/zgodx/images/restart.png" border="0" /></a>
                    </td>
                </tr>
            </table>
         </td>
    </tr>
</table>

</td><td>

<table class="zgrid" border="0" cellpadding="0" cellspacing="0">
	<tr>
    	<td align="left"><img src="modules/zgodx/images/apache.png" /></td>
        <td>	
            <table class="zgrid" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="center">'.$astatus.'</td>
                </tr>
                <tr>
                    <td>';

$end.='
                   &nbsp;<a href="javascript:void(0)"
                    	onClick="javascript:toggle();
                                 document.frmService.getService.value=\'RESTARTAPACHE\';
                        	     document.frmService.getTime;
                        		 document.frmService.submit();"
                        title="Restart Apache"><img src="modules/zgodx/images/restart.png" border="0"/>';
$end.='            </td>
                </tr>
            </table>
         </td>
    </tr>
</table>

</td></tr></table>


';

return $end;
}
?>