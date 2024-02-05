<?php

/**
 * Sentora Project (http://www.sentora.org/)
 * Module maintained by TGates@sentora.org
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 * 
 * ZPanel - A Cross-Platform Open-Source Web Hosting Control panel.
 * 
 * @package ZPanel
 * @version $Id$
 * @author Bobby Allen - ballen@zpanelcp.com (zGodX update - JK - jkmods.tk)
 * @copyright (c) 2008-2011 ZPanel Group - http://www.zpanelcp.com/
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License v3
 *
 * This program (ZPanel) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
 
class module_controller
{

    static function getModuleName()
	{
        $module_name = ui_module::GetModuleName();
        return $module_name;
    }

    static function getModuleIcon()
	{
        global $controller;
        $module_icon = "modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }

    static function getModuleDesc()
	{
        $message = ui_language::translate(ui_module::GetModuleDescription());
        return $message;
    }


    static function getCSFR_Tag()
	{
        return runtime_csfr::Token();
    }

    static function getzGodX_formdata()
	{
		include_once('formdata.php');
		return zGodX_form_data();
    }

    static function getzGodX_css_js()
	{
		include_once('style.php');
		return zGodX_css_js_data();
    }

    static function getzGodX_server()
	{
		include_once('modules/zgodx/code/functions.php');
		include_once('modules/zgodx/main/server.php');
		return zGodX_server_data();
    }
  

    static function GetzGodX_main()
	{
		if(isset($_POST['getMain']))
		{
			$pluginpath = 'modules/zgodx/main';
			include_once ($pluginpath.'/'.$_POST['getMain'].'.php');
			return zGodX_main_data();
		}
      
      if(!isset($_POST['getPlugin']))
	  {
		  include_once ('modules/zgodx/main/main.php');
		  return zGodX_main_data();
      }
    }

    static function GetzGodX_tree()
	{
		$tabla= '<tr><th width="175px">Menu</th></tr>
		<tr><td width="175px"><a href="?module=zgodx">Summary</a><br>
		&raquo; <a href="javascript:void(0)" onClick="document.frmMenu.getMain.value=\'account\'; document.frmMenu.submit();">Accounts</a><br />
		&raquo; <a href="javascript:void(0)" onClick="document.frmMenu.getMain.value=\'vhosts\'; document.frmMenu.submit();">Virtual Hosts</a><br />
		&raquo; <a href="javascript:void(0)" onClick="document.frmMenu.getMain.value=\'packages\'; document.frmMenu.submit();">Packages</a><br />
		&raquo; <a href="javascript:void(0)" onClick="document.frmMenu.getMain.value=\'mail\'; document.frmMenu.submit();">MailBoxes</a><br />
		&raquo; <a href="javascript:void(0)" onClick="document.frmMenu.getMain.value=\'forwarders\'; document.frmMenu.submit();">Forwarders</a><br />
		&raquo; <a href="javascript:void(0)" onClick="document.frmMenu.getMain.value=\'aliases\'; document.frmMenu.submit();">Aliases</a><br />
		&raquo; <a href="javascript:void(0)" onClick="document.frmMenu.getMain.value=\'mysql\'; document.frmMenu.submit();">Databases</a><br />
		&raquo; <a href="javascript:void(0)" onClick="document.frmMenu.getMain.value=\'ftp\'; document.frmMenu.submit();">FTP Accounts</a><br />
		&raquo; <a href="javascript:void(0)" onClick="document.frmMenu.getMain.value=\'crons\'; document.frmMenu.submit();">Cron Jobs</a><br />
		&raquo; <a href="javascript:void(0)" onClick="document.frmMenu.getMain.value=\'bw\'; document.frmMenu.submit();">Bandwidth</a><br />
		&raquo; <a href="javascript:void(0)" onClick="document.frmMenu.getMain.value=\'disk\'; document.frmMenu.submit();">Disk Usage</a><br />
		&raquo; <a href="javascript:void(0)" onClick="document.frmMenu.getMain.value=\'distlists\'; document.frmMenu.submit();">Distribution Lists</a><br />
		&raquo; <a href="javascript:void(0)" onClick="document.frmMenu.getMain.value=\'email_reports\'; document.frmMenu.submit();">Email Reports</a><br />
		<!-- TG - Stats not working
		&raquo; <a href="javascript:void(0)" onClick="document.frmMenu.getMain.value=\'webalizer_stats\'; document.frmMenu.submit();">Webalizer Stats</a><br />
		-->
		<!-- TG - Logs not working if using file instead of DB
		&raquo; <a href="javascript:void(0)" onClick="document.frmMenu.getMain.value=\'zpanel_logs\'; document.frmMenu.submit();">Sentora Logs</a><br />
		-->
		&raquo; <a href="javascript:void(0)" onClick="document.frmMenu.getMain.value=\'deleted_records\'; document.frmMenu.submit();">Deleted Records</a><br /></td>
		</tr>
		';
		return $tabla;
	}

    static function getDonation()
	{
        $donation = '<br /><font face="ariel" size="2">Donate to module maintainer:
		<form action="https://www.paypal.com/donate" method="post" target="_blank">
		<input type="hidden" name="hosted_button_id" value="MCDRPGAZFNEMY" />
		<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" height="20" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
		<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
		</form>
		</font>';
        return $donation;
    }
	
    static function getCopyright()
	{
        $copyright = '<font face="ariel" size="2">' . ui_module::GetModuleName() . ' v1.1.6 &copy; 2013-' . date("Y") . ' maintained by <a target="_blank" href="http://forums.sentora.org/member.php?action=profile&uid=2">TGates</a> for <a target="_blank" href="http://sentora.org">Sentora Control Panel</a> &#8212; Help support future development of this module and donate today!</font>';
        return $copyright;
    }
}
?>