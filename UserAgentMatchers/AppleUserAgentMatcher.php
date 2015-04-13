<?php
/**
 * Copyright (c) 2015 ScientiaMobile, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the COPYING.txt file distributed with this package.
 *
 * @package    WURFL_UserAgentMatcher
 * @copyright  ScientiaMobile, Inc.
 * @author     Steve Kamerman <steve AT scientiamobile.com>
 * @license    GNU Affero General Public License
 * @version    $id$
 */
/**
 * Provides a specific user agent matching technique
 * @package TeraWurflUserAgentMatchers
 */
class AppleUserAgentMatcher extends UserAgentMatcher {
	
	/**
	* This flag tells the WurflLoader that the User Agent may be permanantly 
	* altered during matching
	* @var boolean
	*/
	public $runtime_normalization = true;
	
	public static $constantIDs = array(
		'apple_ipod_touch_ver1',
		'apple_ipod_touch_ver2',
		'apple_ipod_touch_ver3',
		'apple_ipod_touch_ver4',
		'apple_ipod_touch_ver5',
		'apple_ipod_touch_ver6',
		'apple_ipod_touch_ver7',
		'apple_ipod_touch_ver8',
		'apple_ipod_touch_ver9',
				
		'apple_ipad_ver1',
		'apple_ipad_ver1_subua32',
		'apple_ipad_ver1_sub42',
		'apple_ipad_ver1_sub5',
		'apple_ipad_ver1_sub6',
		'apple_ipad_ver1_sub7',
		'apple_ipad_ver1_sub8',
		'apple_ipad_ver1_sub9',
				
		'apple_iphone_ver1',
		'apple_iphone_ver2',
		'apple_iphone_ver3',
		'apple_iphone_ver4',
		'apple_iphone_ver5',
		'apple_iphone_ver6',
		'apple_iphone_ver7',
		'apple_iphone_ver8',
		'apple_iphone_ver9',
	
		//iOS HW IDs
		'apple_ipad_ver1_subhw1',
		'apple_ipad_ver1_sub42_subhw1',
		'apple_ipad_ver1_sub43_subhw1',
		'apple_ipad_ver1_sub43_subhw2',
		'apple_ipad_ver1_sub51_subhw1',
		'apple_ipad_ver1_sub51_subhw2',
		'apple_ipad_ver1_sub51_subhw3',
		'apple_ipad_ver1_sub5_subhw1',
		'apple_ipad_ver1_sub5_subhw2',
		'apple_ipad_ver1_sub6_subhw2',
		'apple_ipad_ver1_sub6_subhw3',
		'apple_ipad_ver1_sub6_subhw4',
		'apple_ipad_ver1_sub61_subhw2',
		'apple_ipad_ver1_sub61_subhw3',
		'apple_ipad_ver1_sub61_subhw4',
		'apple_ipad_ver1_sub61_subhwmini1',
		'apple_ipad_ver1_sub6_subhwmini1',
		'apple_ipad_ver1_sub7_subhw2',
		'apple_ipad_ver1_sub7_subhw3',
		'apple_ipad_ver1_sub7_subhw4',
		'apple_ipad_ver1_sub7_subhwmini1',
		'apple_ipad_ver1_sub7_subhwmini2',
		'apple_ipad_ver1_sub7_subhwair',
		'apple_ipad_ver1_sub71_subhw2',
		'apple_ipad_ver1_sub71_subhw3',
		'apple_ipad_ver1_sub71_subhw4',
		'apple_ipad_ver1_sub71_subhwmini1',
		'apple_ipad_ver1_sub71_subhwmini2',
		'apple_ipad_ver1_sub71_subhwair',
		'apple_ipad_ver1_sub8_subhw2',
		'apple_ipad_ver1_sub8_subhw3',
		'apple_ipad_ver1_sub8_subhw4',
		'apple_ipad_ver1_sub8_subhwair',
		'apple_ipad_ver1_sub8_subhwmini1',
		'apple_ipad_ver1_sub8_subhwmini2',
		'apple_ipad_ver1_sub8_1_subhw2',
		'apple_ipad_ver1_sub8_1_subhw3',
		'apple_ipad_ver1_sub8_1_subhw4',
		'apple_ipad_ver1_sub8_1_subhwair',
		'apple_ipad_ver1_sub8_1_subhwair2',
		'apple_ipad_ver1_sub8_1_subhwmini1',
		'apple_ipad_ver1_sub8_1_subhwmini2',
		'apple_ipad_ver1_sub8_1_subhwmini3',
		'apple_ipad_ver1_sub8_2_subhw2',
		'apple_ipad_ver1_sub8_2_subhw3',
		'apple_ipad_ver1_sub8_2_subhw4',
		'apple_ipad_ver1_sub8_2_subhwair',
		'apple_ipad_ver1_sub8_2_subhwair2',
		'apple_ipad_ver1_sub8_2_subhwmini1',
		'apple_ipad_ver1_sub8_2_subhwmini2',
		'apple_ipad_ver1_sub8_2_subhwmini3',
		'apple_ipad_ver1_sub8_3_subhw2',
		'apple_ipad_ver1_sub8_3_subhw3',
		'apple_ipad_ver1_sub8_3_subhw4',
		'apple_ipad_ver1_sub8_3_subhwair',
		'apple_ipad_ver1_sub8_3_subhwair2',
		'apple_ipad_ver1_sub8_3_subhwmini1',
		'apple_ipad_ver1_sub8_3_subhwmini2',
		'apple_ipad_ver1_sub8_3_subhwmini3',
		
		'apple_iphone_ver1_subhw2g',
		'apple_iphone_ver2_subhw2g',
		'apple_iphone_ver2_subhw3g',
		'apple_iphone_ver2_1_subhw2g',
		'apple_iphone_ver2_1_subhw3g',
		'apple_iphone_ver2_2_subhw2g',
		'apple_iphone_ver2_2_subhw3g',
		'apple_iphone_ver3_subhw2g',
		'apple_iphone_ver3_subhw3g',
		'apple_iphone_ver3_subhw3gs',
		'apple_iphone_ver3_1_subhw2g',
		'apple_iphone_ver3_1_subhw3g',
		'apple_iphone_ver3_1_subhw3gs',
		'apple_iphone_ver4_subhw3g',
		'apple_iphone_ver4_subhw3gs',
		'apple_iphone_ver4_subhw4',
		'apple_iphone_ver4_1_subhw3g',
		'apple_iphone_ver4_1_subhw3gs',
		'apple_iphone_ver4_1_subhw4',
		'apple_iphone_ver4_2_subhw3g',
		'apple_iphone_ver4_2_subhw3gs',
		'apple_iphone_ver4_2_subhw4',
		'apple_iphone_ver4_3_subhw3gs',
		'apple_iphone_ver4_3_subhw4',
		'apple_iphone_ver5_subhw3gs',
		'apple_iphone_ver5_subhw4',
		'apple_iphone_ver5_subhw4s',
		'apple_iphone_ver5_1_subhw3gs',
		'apple_iphone_ver5_1_subhw4',
		'apple_iphone_ver5_1_subhw4s',
		'apple_iphone_ver6_subhw3gs',
		'apple_iphone_ver6_subhw4',
		'apple_iphone_ver6_subhw4s',
		'apple_iphone_ver6_subhw5',
		'apple_iphone_ver6_1_subhw3gs',
		'apple_iphone_ver6_1_subhw4',
		'apple_iphone_ver6_1_subhw4s',
		'apple_iphone_ver6_1_subhw5',
		'apple_iphone_ver7_subhw4',
		'apple_iphone_ver7_subhw4s',
		'apple_iphone_ver7_subhw5',
		'apple_iphone_ver7_subhw5c',
		'apple_iphone_ver7_subhw5s',
		'apple_iphone_ver7_1_subhw4',
		'apple_iphone_ver7_1_subhw4s',
		'apple_iphone_ver7_1_subhw5',
		'apple_iphone_ver7_1_subhw5c',
		'apple_iphone_ver7_1_subhw5s',
		'apple_iphone_ver8_subhw4s',
		'apple_iphone_ver8_subhw5',
		'apple_iphone_ver8_subhw5c',
		'apple_iphone_ver8_subhw5s',
		'apple_iphone_ver8_subhw6',
		'apple_iphone_ver8_subhw6plus',
		'apple_iphone_ver8_subua802_subhw4s',
		'apple_iphone_ver8_subua802_subhw5',
		'apple_iphone_ver8_subua802_subhw5c',
		'apple_iphone_ver8_subua802_subhw5s',
		'apple_iphone_ver8_subua802_subhw6',
		'apple_iphone_ver8_subua802_subhw6plus',
		'apple_iphone_ver8_1_subhw4s',
		'apple_iphone_ver8_1_subhw5',
		'apple_iphone_ver8_1_subhw5c',
		'apple_iphone_ver8_1_subhw5s',
		'apple_iphone_ver8_1_subhw6',
		'apple_iphone_ver8_1_subhw6plus',
		'apple_iphone_ver8_2_subhw4s',
		'apple_iphone_ver8_2_subhw5',
		'apple_iphone_ver8_2_subhw5c',
		'apple_iphone_ver8_2_subhw5s',
		'apple_iphone_ver8_2_subhw6',
		'apple_iphone_ver8_2_subhw6plus',
		'apple_iphone_ver8_3_subhw4s',
		'apple_iphone_ver8_3_subhw5',
		'apple_iphone_ver8_3_subhw5c',
		'apple_iphone_ver8_3_subhw5s',
		'apple_iphone_ver8_3_subhw6',
		'apple_iphone_ver8_3_subhw6plus',

		'apple_ipod_touch_ver1_subhw1',
		'apple_ipod_touch_ver2_subhw1',
		'apple_ipod_touch_ver2_1_subhw1',
		'apple_ipod_touch_ver2_1_subhw2',
		'apple_ipod_touch_ver2_2_subhw1',
		'apple_ipod_touch_ver2_2_subhw2',
		'apple_ipod_touch_ver3_subhw1',
		'apple_ipod_touch_ver3_subhw2',
		'apple_ipod_touch_ver3_1_subhw1',
		'apple_ipod_touch_ver3_1_subhw2',
		'apple_ipod_touch_ver3_1_subhw3',
		'apple_ipod_touch_ver4_subhw2',
		'apple_ipod_touch_ver4_subhw3',
		'apple_ipod_touch_ver4_1_subhw2',
		'apple_ipod_touch_ver4_1_subhw3',
		'apple_ipod_touch_ver4_1_subhw4',
		'apple_ipod_touch_ver4_2_subhw2',
		'apple_ipod_touch_ver4_2_subhw3',
		'apple_ipod_touch_ver4_2_subhw4',
		'apple_ipod_touch_ver4_3_subhw3',
		'apple_ipod_touch_ver4_3_subhw4',
		'apple_ipod_touch_ver5_subhw3',
		'apple_ipod_touch_ver5_subhw4',
		'apple_ipod_touch_ver5_1_subhw3',
		'apple_ipod_touch_ver5_1_subhw4',
		'apple_ipod_touch_ver6_subhw3',
		'apple_ipod_touch_ver6_subhw4',
		'apple_ipod_touch_ver6_subhw5',
		'apple_ipod_touch_ver6_1_subhw4',
		'apple_ipod_touch_ver6_1_subhw5',
		'apple_ipod_touch_ver7_subhw5',
		'apple_ipod_touch_ver7_1_subhw5',
		'apple_ipod_touch_ver8_subhw5',
		'apple_ipod_touch_ver8_1_subhw5',
		'apple_ipod_touch_ver8_2_subhw5',
		'apple_ipod_touch_ver8_3_subhw5',
	);
	
	// iOS hardware mappings
	public static $iphoneDeviceMap = array(
		'1,1' => '2g',
		'1,2' => '3g',
		'2,1' => '3gs',
		'3,1' => '4',
		'3,2' => '4',
		'3,3' => '4',
		'4,1' => '4s',
		'5,1' => '5',
		'5,2' => '5',
		'5,3' => '5c',
		'5,4' => '5c',
		'6,1' => '5s',
		'6,2' => '5s',
		'7,1' => '6plus',
		'7,2' => '6',
	);
	
	public static $ipadDeviceMap = array(
		'1,1' => '1',
		'2,1' => '2',
		'2,2' => '2',
		'2,3' => '2',
		'2,4' => '2',
		'2,5' => 'mini1',
		'2,6' => 'mini1',
		'2,7' => 'mini1',
		'3,1' => '3',
		'3,2' => '3',
		'3,3' => '3',
		'3,4' => '4',
		'3,5' => '4',
		'3,6' => '4',
		'4,1' => 'air',
		'4,2' => 'air',
		'4,3' => 'air',
		'4,4' => 'mini2',
		'4,5' => 'mini2',
		'4,6' => 'mini2',
		'4,7' => 'mini3',
		'4,8' => 'mini3',
		'4,9' => 'mini3',
		'5,3' => 'air2',
		'5,4' => 'air2',
	);
	
	public static $ipodDeviceMap = array(
		'1,1' => '1',
		'2,1' => '2',
		'3,1' => '3',
		'4,1' => '4',
		'5,1' => '5',
	);
	
	public static function canHandle(TeraWurflHttpRequest $httpRequest) {
		if ($httpRequest->isDesktopBrowser()) return false;
		return ($httpRequest->user_agent->contains('Mozilla/5') && $httpRequest->user_agent->contains(array('iPhone', 'iPod', 'iPad')));
	}
	
	public function applyConclusiveMatch() {
		
		// Normalize Skype SDK UAs
		if (preg_match('#^iOSClientSDK/\d+\.+[0-9\.]+ +?\((Mozilla.+)\)$#', $this->userAgent, $matches)) {
			$this->userAgent->set($matches[1]);
		}
		
		// Normalize iOS {Ver} style UAs
		//Eg: Mozilla/5.0 (iPhone; U; CPU iOS 7.1.2 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Safari/528.16
		if (preg_match("#CPU iOS \d+?\.\d+?#", $this->userAgent)) {
			$ua = $this->userAgent->contains("iPad") ? str_replace("CPU iOS", "CPU OS", $this->userAgent): str_replace("CPU iOS", "CPU iPhone OS", $this->userAgent);
			if (preg_match("#(CPU(?: iPhone)? OS [\d\.]+ like)#", $ua, $matches)) {
				$versionUnderscore = str_replace(".", "_", $matches[1]);
				$ua = str_replace(" U;", "", $ua);
				$ua = preg_replace("#CPU(?: iPhone)? OS ([\d\.]+) like#", $versionUnderscore, $ua);
				$this->userAgent->set($ua);  	
			}
		}
	
		// Attempt to find hardware version
		$device_version = null;
		if (preg_match('#(?:iPhone|iPad|iPod) ?(\d,\d)#', $this->userAgent, $matches)) {
			// Check for iPod first since they contain 'iPhone'
			if ($this->userAgent->contains("iPod")) {
				if (array_key_exists($matches[1], self::$ipodDeviceMap)) {
					$device_version = str_replace(array_keys(self::$ipodDeviceMap), array_values(self::$ipodDeviceMap), $matches[1]);
				}	
			} else if ($this->userAgent->contains("iPad")) {
				if (array_key_exists($matches[1], self::$ipadDeviceMap)) {
					$device_version = str_replace(array_keys(self::$ipadDeviceMap), array_values(self::$ipadDeviceMap), $matches[1]);
				}	
			} else if ($this->userAgent->contains("iPhone")) {
				if (array_key_exists($matches[1], self::$iphoneDeviceMap)) {
					$device_version = str_replace(array_keys(self::$iphoneDeviceMap), array_values(self::$iphoneDeviceMap), $matches[1]);
				}
			// Set $device_version to null if UA contains unrecognized hardware version or does not satisfy any of the above 'if' statements 
			} else {
				$device_version = null;
			}
		}
		
		$tolerance = $this->userAgent->indexOf('_');
		if ($tolerance !== false) {
			// The first char after the first underscore
			$tolerance++;
		} else {
			$index = $this->userAgent->indexOf('like Mac OS X;');
			if ($index !== false) {
				// Step through the search string to the semicolon at the end
				$tolerance = $index + 14;
			} else {
				// Non-typical UA, try full length match
				$tolerance = $this->userAgent->length();
			}
		}
		
		$ris_id = $this->risMatch($tolerance);
		
		//Assemble and check iOS HW ID
		if ($device_version !== null) {
			$test_id = $ris_id."_subhw".$device_version;
			if (in_array($test_id, self::$constantIDs)) {
				return $test_id;
			}
		}

		return $ris_id;
	}
	
	public function applyRecoveryMatch() {
		if (preg_match('/ (\d)_(\d)[ _]/', $this->userAgent, $matches)) {
			$major_version = (int)$matches[1];
			$minor_version = (int)$matches[2];
		} else {
			$major_version = -1;
			$minor_version = -1;
		}
		// Check iPods first since they also contain 'iPhone'
		if ($this->userAgent->contains('iPod')) {
			$deviceID = 'apple_ipod_touch_ver'.$major_version;
			if (in_array($deviceID, self::$constantIDs)) {
				return $deviceID;
			} else {
				return 'apple_ipod_touch_ver1';
			}
			
		// Now check for iPad
		} else if ($this->userAgent->contains('iPad')) {
			$deviceID = 'apple_ipad_ver1_sub'.$major_version;
			
			if ($major_version == 3) {
				return 'apple_ipad_ver1_subua32';
			} else if ($major_version == 4) {
				return 'apple_ipad_ver1_sub42';
			}
			
			if (in_array($deviceID, self::$constantIDs)) {
				return $deviceID;
			} else {
				return 'apple_ipad_ver1';
			}
			
		// Check iPhone last
		} else if ($this->userAgent->contains('iPhone')) {
			$deviceID = 'apple_iphone_ver'.$major_version;
			if (in_array($deviceID, self::$constantIDs)) {
				return $deviceID;
			} else {
				return 'apple_iphone_ver1';
			}
		}
		return WurflConstants::NO_MATCH;
	}
}
