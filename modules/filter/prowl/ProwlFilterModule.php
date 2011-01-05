<?php
class ProwlFilterModule extends FilterModule {
  public function onAnyError($aOptions) {
    $sAddress = Settings::getSetting('developer', 'email', false);
    if(!$sAddress) {
      $sAddress = Settings::getSetting('domain_holder', 'email', false);
    }
    if(!$sAddress) {
      return;
    }
    $aProwlConfig = Settings::getSetting('prowl', 'users', array());
    if(!isset($aProwlConfig[$sAddress])) {
      return;
    }
    $aError = $aOptions[0];
    $aProwlConfig = $aProwlConfig[$sAddress];
    $sUsername = @$aProwlConfig['user'];
    $sPassword = @$aProwlConfig['password'];
    $aProwlParams = array();
    $aProwlParams['application'] = "rapila on ".$aError['host'];
    $aProwlParams['event'] = "Error in ".MAIN_DIR_FE.$aError['path'];
    $aProwlParams['description'] = StringUtil::truncate($aError['message'], 400);
    $sParams = http_build_query($aProwlParams);
    @file_get_contents("https://$sUsername:$sPassword@prowl.weks.net/api/add_notification.php?$sParams");
  }
}