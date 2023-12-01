<?php
function zGodX_main_data(){
      $end ='';

			$user_array = ctrl_users::GetUserDetail();
			$user_x  = $user_array['username'];
			$user_x_id = $user_array['userid'];
      global $zdbh;
      
if ($user_x == "zadmin" || $user_x_id ==1){ #If zadmin we get everything

$end.='
<em>zGodX &raquo; <b>Zpanel Logs</b></em><br><br>

           	<b>ZPanel Logs for this Month</b>

            <div id="logs_zpanel" style="overflow:auto; height:500px;">
            <table class="table table-striped">';
			
			$lastmonth  = time() - (30 * 24 * 60 * 60);
			$lastmonth = date("Y-m-d H:i:s", $lastmonth); 
			$lastweek = time() - (7 * 24 * 60 * 60);
			$lastweek = date("Y-m-d H:i:s", $lastweek); 
			$sql 	   = "SELECT * FROM x_logs WHERE lg_when_ts >= '".$lastmonth ."' ORDER BY lg_when_ts DESC";
			$list  = $zdbh->query($sql);
			$list->setFetchMode(PDO::FETCH_ASSOC);
			$row   = $list->fetch();
			$total =$zdbh->query($sql)->rowCount();

			do{ 

			$sql 	 = "SELECT ac_user_vc, ac_id_pk FROM x_accounts WHERE ac_id_pk ='" .$row['lg_user_fk']."'";
			$listacc  = $zdbh->query($sql);
			$listacc->setFetchMode(PDO::FETCH_ASSOC);
			$rowacc   = $listacc->fetch();
			
			$date = "".$row['lg_when_ts']." "; 
			
      $end.='      	<tr>
                	<td>'.$rowacc['ac_user_vc'].'</td>
                	<td>'.$date.$row['lg_detail_tx'].'</td>
                </tr>';
                
      } while($row = $list->fetch());        
                
    $end.='       </table> 
            </div>';

  } else { 
  $end.='
  <em>zGodX &raquo; <b>Zpanel Logs</b></em><br><br>
  <b>You don\'t have permission to access this module</b>';
  }
 
return $end;
} 
?>