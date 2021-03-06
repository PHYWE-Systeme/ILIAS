<?php
/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
* GUI class ilSCORMOfflineModeGUI
*
* GUI class for scorm offline player connection
*
* @author Uwe Kohnle <kohnle@internetlehrer-gmbh.de>
* @version $Id: class.ilSCORMOfflineModeGUI.php  $
*
*
*/
class ilSCORMOfflineModeGUI
{
	var $lmId;
	var $clientIdSop;
	var $offlineMode;
	var $online_icon;
	var $offline_icon;
	var $icon;
	
	function __construct($type) {
		global $ilias, $tpl, $lng, $ilCtrl;
		include_once "./Modules/ScormAicc/classes/class.ilSCORMOfflineMode.php";
		$this->ilias = $ilias;
		$this->tpl = $tpl;
		$lng->loadLanguageModule("sop");
		$this->lng = $lng;
		$this->ctrl = $ilCtrl;
		$this->ctrl->saveParameter($this, "ref_id");
		$this->offlineMode = new ilSCORMOfflineMode();
		$this->online_icon = 'icon_lm.svg';
		$this->offline_icon = 'icon_slm_offline.svg';
		$this->icon = $this->online_icon;
	}
	
	function executeCommand()
	{
		global $tpl, $ilCtrl;
		$this->lmId = ilObject::_lookupObjectId($_GET["ref_id"]);
		$this->clientIdSop = $this->offlineMode->getClientIdSop();
		$cmd = $ilCtrl->getCmd();
		$this->setOfflineModeTabs($cmd);
		switch($cmd){
			case 'offlineMode_il2sopContent':
				ilUtil::deliverFile(ilUtil::getDataDir()."/lm_data/lm_".$this->lmId.".zip","lm_".$this->lmId.".zip");
				break;
			case 'offlineMode_il2sop':
				$this->offlineMode->il2sop();
				break;
			case 'offlineMode_il2sopStop':
				$this->offlineMode->setOfflineMode("online");
				$this->view($this->offlineMode->getOfflineMode(),"msg_export_failure");
				break;
			case 'offlineMode_il2sopOk':
				$this->offlineMode->setOfflineMode("offline");
				$this->view($this->offlineMode->getOfflineMode(),"msg_export_ok");
				break;
			case 'offlineMode_sop2il':
				$this->offlineMode->sop2il();
				break;
			case 'offlineMode_sop2ilStop':
				$this->offlineMode->setOfflineMode("offline");
				$this->view($this->offlineMode->getOfflineMode(),"msg_push_tracking_failure");
				break;
			case 'offlineMode_sop2ilOk':
				$this->offlineMode->setOfflineMode("online");
				$this->view($this->offlineMode->getOfflineMode(),"msg_push_tracking_ok");
				break;
			default:
				if ($this->offlineMode->getOfflineMode() == "il2sop") $this->offlineMode->setOfflineMode("online");
				$this->view($this->offlineMode->getOfflineMode());
				break;
		}
	}
	
	function view($offline_mode,$message="") {
		global $tpl;
		$this->icon = ($offline_mode == "offline") ? $this->offline_icon : $this->online_icon;
		// Fill meta header tags
		$tpl->setCurrentBlock('mh_meta_item');
		$tpl->setVariable('MH_META_NAME','require-sop-version');
		$tpl->setVariable('MH_META_CONTENT',"0.1");
		$tpl->parseCurrentBlock();
//		$tpl->addJavascript('./Modules/ScormAicc/scripts/sopConnector.js');
		$tpl->addBlockFile("ADM_CONTENT", "adm_content", "tpl.scorm_offline_mode.html", "Modules/ScormAicc");
		$tpl->setCurrentBlock('offline_content');
		$tpl->setTitleIcon(ilUtil::getImagePath($this->icon));
	
		$tpl->setVariable("CHECK_SYSTEM_REQUIREMENTS",$this->lng->txt('sop_check_system_requirements'));
		$tpl->setVariable("FIREFOX_REQUIRED",$this->lng->txt('sop_firefox_required'));
		$tpl->setVariable("MSG_YOUR_BROWSER",$this->lng->txt('sop_msg_your_browser'));
		$tpl->setVariable("MSG_FIREFOX_NOT_PORTABLE",$this->lng->txt('sop_msg_firefox_not_portable'));
		$tpl->setVariable("MSG_FIREFOX_VERSION_NOT_SUPPORTED",$this->lng->txt('sop_msg_firefox_version_not_supported'));
		$tpl->setVariable("MSG_FIREFOX_DOWNLOAD_WIN",$this->lng->txt('sop_msg_firefox_download_win'));
		$tpl->setVariable("MSG_FIREFOX_DOWNLOAD_LINUX",$this->lng->txt('sop_msg_firefox_download_linux'));
		$tpl->setVariable("MSG_FIREFOX_DOWNLOAD_MAC",$this->lng->txt('sop_msg_firefox_download_mac'));
		$tpl->setVariable("MSG_FIREFOX_AFTER_DOWNLOAD",$this->lng->txt('sop_msg_firefox_after_download'));
		$tpl->setVariable("MSG_FIREFOX_CONFIG",$this->lng->txt('sop_msg_firefox_config'));
		$tpl->setVariable("MSG_FIREFOX_HTML5",$this->lng->txt('sop_msg_firefox_html5'));
		$tpl->setVariable("ALREADY_EXPORTED",$this->lng->txt('sop_already_exported'));
		$tpl->setVariable("XPI_INSTALL",sprintf($this->lng->txt('sop_xpi_install'),"<a href='./Modules/ScormAicc/sop/sop.xpi'>sop.xpi</a>",false));
		$tpl->setVariable("RELOAD_PAGE",$this->lng->txt('sop_reload_page'));
		$tpl->setVariable("TEXT_EXPORT",$this->lng->txt('sop_text_export'));
		$tpl->setVariable("EXPORT",$this->lng->txt('sop_export'));
		$tpl->setVariable("DESC_EXPORT",$this->lng->txt('sop_desc_export'));
		$tpl->setVariable("TEXT_START_OFFLINE",$this->lng->txt('sop_text_start_offline'));
		$tpl->setVariable("START_OFFLINE",$this->lng->txt('sop_start_offline'));
		$tpl->setVariable("TEXT_START_SOM",$this->lng->txt('sop_text_start_som'));
		$tpl->setVariable("START_SOM",$this->lng->txt('sop_start_som'));
		$tpl->setVariable("TEXT_PUSH_TRACKING",$this->lng->txt('sop_text_push_tracking'));
		$tpl->setVariable("PUSH_TRACKING",$this->lng->txt('sop_push_tracking'));
		$tpl->setVariable("MSG_LM_NOT_EXISTS",$this->lng->txt('sop_msg_lm_not_exists'));
		$tpl->setVariable("MSG_CLOSE_LM",$this->lng->txt('sop_msg_close_lm'));
		$tpl->setVariable("MSG_EXPORT_CONTENT",$this->lng->txt('sop_msg_export_content'));
		$tpl->setVariable("MSG_EXPORT_TRACKING",$this->lng->txt('sop_msg_export_tracking'));
		$tpl->setVariable("MSG_PUSH_TRACKING",$this->lng->txt('sop_msg_push_tracking'));
		$tpl->setVariable("MSG_PUSH_TRACKING_OK",$this->lng->txt('sop_msg_push_tracking_ok'));

		$tpl->setVariable("CLIENT_ID",CLIENT_ID);
		$tpl->setVariable("CLIENT_ID_SOP",$this->clientIdSop);
		$tpl->setVariable("REF_ID",$_GET['ref_id']);
		$tpl->setVariable("LM_ID",$this->lmId);
		$tpl->setVariable("OFFLINE_MODE",$offline_mode);
		if ($message != "") $tpl->setVariable("MESSAGE_RESULT",$this->lng->txt('sop_'.$message));
//		if ($message==""msg_export_failure,msg_export_ok,msg_push_tracking_failure,msg_push_tracking_ok
		
		$tpl->parseCurrentBlock();
		$tpl->show();
	}
	function importStop() {
	}
	
	function setOfflineModeTabs($a_active)
	{	
		global $ilTabs, $ilLocator,$tpl;
		$icon = ($this->offlineMode->getOfflineMode() == "online") ? "icon_lm.svg" : "icon_slm_offline.svg";
		$tabTitle = $this->lng->txt("offline_mode");
		$thisurl =$this->ctrl->getLinkTarget($this, $a_active);
		$ilTabs->addTab($a_active, $tabTitle, $thisurl);
		$ilTabs->activateTab($a_active);
		$tpl->getStandardTemplate();
		$tpl->setTitle(ilObject::_lookupTitle($this->lmId));
		$tpl->setTitleIcon(ilUtil::getImagePath($icon));
		$ilLocator->addRepositoryItems();
		$ilLocator->addItem(ilObject::_lookupTitle($this->lmId),$thisurl);
		$tpl->setLocator();
	}
}
?>
