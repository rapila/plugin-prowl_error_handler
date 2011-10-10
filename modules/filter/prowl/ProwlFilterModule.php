<?php
class ProwlFilterModule extends FilterModule {
  public function onAnyError($aOptions) {
    $aProwlConfig = Settings::getSetting('prowl', 'users', array());
		$aKeys = array();
		foreach($aProwlConfig as $aConfig) {
			if(in_array(ErrorHandler::getEnvironment(), $aConfig['environments'])) {
				$aKeys[] = $aConfig['key'];
			}
		}
		if(count($aKeys) == 0) {
			return;
		}
    $aError = &$aOptions[0];
		
    $aProwlParams = array();
    $aProwlParams['apikey'] = implode(',', $aKeys);
    $aProwlParams['application'] = "Rapila on ".$aError['host'];
    $aProwlParams['event'] = "Error on in ".$aError['path'];
    $aProwlParams['url'] = 'http://'.$aError['host'].$aError['path'];
    $aProwlParams['description'] = StringUtil::truncate($aError['message'], 800);
		
    $sParams = http_build_query($aProwlParams);
		$rCurl = curl_init('https://api.prowlapp.com/publicapi/add');
		curl_setopt($rCurl, CURLOPT_POSTFIELDS, $sParams);
		curl_setopt($rCurl, CURLOPT_POST, 1);
		curl_exec($rCurl);
  }
}
