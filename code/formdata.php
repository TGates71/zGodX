<?php
function zGodX_form_data() {

$end= '
<form name="frmService" id="frmService" action="" method="POST">
<input type="hidden" name="getService" value="">
<input type="hidden" name="getTime" value="'.time().'">
'.runtime_csfr::Token().'
</form>

<form name="frmMenu" id="frmMenu" action="" method="POST">
<input type="hidden" name="getMain" value=""> 
<input type="hidden" name="getTime" value="'.time().'">
'.runtime_csfr::Token().'
</form>

<form name="frmMain" id="frmMain" action="" method="POST">
<input type="hidden" name="getMain" value=""> 
<input type="hidden" name="getEditAcc" value=""> 
<input type="hidden" name="getUser" value="">
<input type="hidden" name="getService" value="">
<input type="hidden" name="getResetQuota" value="">
<input type="hidden" name="getResetQuotaID" value="">
<input type="hidden" name="getLockAcc" value="">
<input type="hidden" name="getLockAccID" value="">
<input type="hidden" name="getShAcc" value="">
<input type="hidden" name="getShAccID" value="">
<input type="hidden" name="getLockAccReason" value="">
<input type="hidden" name="getDeleteFTP" value="">
<input type="hidden" name="getDeleteFTPUSER" value="">
<input type="hidden" name="getDeleteFTPACC" value="">
<input type="hidden" name="getDeleteFTPID" value="">
<input type="hidden" name="getDeleteCRON" value="">
<input type="hidden" name="getDeleteCRONUSERID" value="">
<input type="hidden" name="getDeleteCRONID" value="">
<input type="hidden" name="getToggleMail" value="">
<input type="hidden" name="getToggleMailID" value="">
<input type="hidden" name="getxDailyReport" value="">
<input type="hidden" name="getxDailyReportStatus" value="">
<input type="hidden" name="getxUpdateList" value="">
<input type="hidden" name="getxUpdateListVal" value="">
<input type="hidden" name="getUnDel" value="">
<input type="hidden" name="getUnDelID" value="">
<input type="hidden" name="getTime" value="'.time().'">
'.runtime_csfr::Token().'
</form>';


return $end;
}
?>