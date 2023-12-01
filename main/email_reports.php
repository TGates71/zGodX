<?php
function zGodX_main_data(){
      $end ='';
     
			$user_array = ctrl_users::GetUserDetail();
			$user_x  = $user_array['username'];
			$user_x_id = $user_array['userid'];
      global $zdbh;


$sql = "SELECT ac_email_vc FROM x_accounts WHERE ac_id_pk ='1'";
			$listacc  = $zdbh->query($sql);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch();
$email = $rowacc['ac_email_vc'];


if (isset($_POST['getxDailyReport'])){$end.=zGodxReportCron($_POST['getxDailyReport'], $_POST['getxDailyReportStatus']); }
if (isset($_POST['getxUpdateList'])){$end.=UpdateList($_POST['getxUpdateList'], $_POST['getxUpdateListVal']); }


if ($user_x == "zadmin" || $user_x_id ==1){ #If zadmin we get everything

$sql_crx = "SELECT * FROM x_cronjobs WHERE ct_description_tx ='zGodx_Daily_Reports' AND ct_deleted_ts IS NULL";
$total =$zdbh->query($sql_crx)->rowCount();


  $end.='
  <em>zGodX &raquo; <b>Email Reports</b></em><br><br>
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="top">
      <td align="left">         
           <table class="zgrid" width="100%">
                <tr>
                    <th colspan="2">Daily Report</th>
                  </tr>
          <tr>
                    <td colspan="2">Send a daily report summary to the zadmin and/or defined email address/es .<br /><br />
                    <b>Note:</b><br />
                    <small>This email report will be sent to the address/es listed below. <br />
                    Email address of zadmin is added automatically at the top of the list and cannot be deleted.<br />
                    You may disable the Daily Report cronjob from here, or remove directly from the cron page.</small></td>
                  </tr>
          <tr>
                
                  <td valign="top"><br /><br />
                  ';
                       if($total == 1 ){
                       $status = "<font color=\"green\">Enabled</font>";
                          $end.='       					<a title="Disable Daily Crons" href="javascript:void(0)"
                                                  onClick="document.frmMain.getMain.value=\'email_reports\';
                                                  document.frmMain.getxDailyReport.value=\'CRON\';
                                                  document.frmMain.getxDailyReportStatus.value=\'OFF\';
                                                  document.frmMain.submit();"><center><img src="modules/zgodx/images/cron_off.png" border="0" /></center></a>';
           }else{ 
                      $status = "<font color=\"red\">Disabled</font>";
                        $end.='                   <a title="Enable Daily Crons" href="javascript:void(0)"
                                                  onClick="document.frmMain.getMain.value=\'email_reports\';
                                                  document.frmMain.getxDailyReport.value=\'CRON\';
                                                  document.frmMain.getxDailyReportStatus.value=\'ON\';
                                                  document.frmMain.submit();"><center><img src="modules/zgodx/images/cron_on.png" border="0" /></center></a>';
             } 
              				    
  $end.='       <br />
                <center>Daily report is: <br /><b>'.$status.'</b>
                <br /><br /><br />
                <a href="#" title="Send report now" onclick="Popup=window.open(\'modules/zgodx/main/crons/zGodx_DailyReport.php\',\'Popup\',\'toolbar=no,location=no,status=no,menubar=yes,scrollbars=yes,resizable=no, width=600,height=600,left=430,top=23\'); return false;">Send report now</a>
                </center>
                <br />
                </td>
                <td>		';
                
        //$sql 	 = "SELECT * FROM x_accounts WHERE ac_id_pk !=1";  OLD one only for accounts
        $sql 	 = "SELECT x_accounts.ac_email_vc AS u_email FROM x_accounts WHERE x_accounts.ac_id_pk !=1 AND x_accounts.ac_deleted_ts IS NULL
                  UNION
                  SELECT x_mailboxes.mb_address_vc AS u_email FROM x_mailboxes WHERE x_mailboxes.mb_deleted_ts IS NULL";
        
        
        
                $listac = $zdbh->query($sql);
                $listac->setFetchMode(PDO::FETCH_ASSOC);
                $rowac   = $listac->fetch();
                $totalac =$zdbh->query($sql)->rowCount();
                
        $end.='<br />Add user to list:&nbsp;&nbsp;&nbsp;
        <select id="adms_names" style="width:300px">';
  $end .='<option value="-select-">Please select</option>';
  do{
  $end .='<option value="'.$rowac['u_email'].'">'.$rowac['u_email'].'</option>';

  } while($rowac = $listac->fetch());


  $end .= '</select>
  &nbsp;
                <a title="Add contact" href="javascript:void(0)"
                onClick="document.frmMain.getMain.value=\'email_reports\';
                document.frmMain.getxUpdateList.value=\'add_user\';
                document.frmMain.getxUpdateListVal.value=document.getElementById(\'adms_names\').value;
                document.frmMain.submit();">
                <button class="btn" id="button" name="add" id="add" value="butt">Add</button>
                </a>
  ';
 					    
					    
					    			    
  $end.='	<br /><br /><br /><i>Sending report to:</i><br />
            <table class="table table-striped">
                <tr>
                      <th colspan="1">Email Address</th>
                      <th colspan="1">Added</th>
                      <th colspan="1"><center>Action/s</center></th>
                  </tr>';

		$end.= '<tr><td>'.$email.'</td><td>N/A</td><td><center>N/A</center></td></tr>';

  	#get info
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
      $end.= '<tr><td>'.$rowre['zg_email_re'].'</td><td>'.date("M d, Y",$rowre['zg_created_re']).'</td><td>
      				<a title="Remove user from list" href="javascript:void(0)"
              onClick="document.frmMain.getMain.value=\'email_reports\';
              document.frmMain.getxUpdateList.value=\'remove_user\';
              document.frmMain.getxUpdateListVal.value=\''.$rowre['zg_email_re'].'\';
              document.frmMain.submit();"><center><img src="modules/zgodx/images/deletesmall.png" border="0" /></center></a>
      </td></tr>';
      } while($rowre = $listre->fetch());
     } 
}
   
  $end.='</table>
                </td>				
          </tr>
              </table>
                <table class="zgrid" width="100%">
              </table> 
          </td>
      </tr>
  </table>';


  } else {
  $end.='
  <em>zGodX &raquo; <b>Email Reports</b></em><br><br>
  <b>You don\'t have permission to access this part.</b>';
  }
 
return $end;
}
?>