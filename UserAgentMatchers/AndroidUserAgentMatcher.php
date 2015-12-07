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
class AndroidUserAgentMatcher extends UserAgentMatcher {
	
	/**
	 * This flag tells the WurflLoader that the User Agent may be permanantly 
	 * altered during matching
	 * @var boolean
	 */
	public $runtime_normalization = true;
	
	public static $constantIDs = array(
		'generic_android',
		'generic_android_ver1_5',
		'generic_android_ver1_6',
		'generic_android_ver2',
		'generic_android_ver2_1',
		'generic_android_ver2_2',
		'generic_android_ver2_3',
		'generic_android_ver4',
		'generic_android_ver4_1',
		'generic_android_ver4_2',
		'generic_android_ver4_3',
		'generic_android_ver4_4',
		'generic_android_ver4_5',
		'generic_android_ver5_0',
		'generic_android_ver5_1',
		'generic_android_ver5_2',
		'generic_android_ver5_3',
		'generic_android_ver6_0',
		'generic_android_ver6_1',

		'generic_android_ver1_5_tablet',
		'generic_android_ver1_6_tablet',
		'generic_android_ver2_tablet',
		'generic_android_ver2_1_tablet',
		'generic_android_ver2_2_tablet',
		'generic_android_ver2_3_tablet',
		'generic_android_ver3_0',
		'generic_android_ver3_1',
		'generic_android_ver3_2',
		'generic_android_ver3_3',
		'generic_android_ver4_tablet',
		'generic_android_ver4_1_tablet',
		'generic_android_ver4_2_tablet',
		'generic_android_ver4_3_tablet',
		'generic_android_ver4_4_tablet',
		'generic_android_ver4_5_tablet',
		'generic_android_ver5_0_tablet',
		'generic_android_ver5_1_tablet',
		'generic_android_ver5_2_tablet',
		'generic_android_ver5_3_tablet',
		'generic_android_ver6_0_tablet',
		'generic_android_ver6_1_tablet',
	);
	
	public static function canHandle(TeraWurflHttpRequest $httpRequest) {
		return !$httpRequest->user_agent->contains('like Android') &&  $httpRequest->user_agent->contains('Android');
	}
	
	public function applyConclusiveMatch() {
		// Apply Version+Model--- matching normalization
		$model = self::getAndroidModel($this->userAgent, false);
		$version = self::getAndroidVersion($this->userAgent, false);
		
		if ($model !== null && $version !== null) {
			$prefix = $version.' '.$model.WurflConstants::RIS_DELIMITER;
			$this->userAgent->set($prefix.$this->userAgent);
			return $this->risMatch(strlen($prefix));
		}
		
		//Return no match for UAs with no extractable Android version, model and that do not start with either Mozilla or Dalvik
		if (!$this->userAgent->startsWith(array('Mozilla','Dalvik'))) {
			return WurflConstants::NO_MATCH;
		}
		
		// Standard RIS Matching
		$tolerance = $this->userAgent->indexOfOrLength(array(' Build/', ' AppleWebKit'));
		return $this->risMatch($tolerance);
	}	
	
	public function applyRecoveryMatch() {
		if ($this->userAgent->contains('Froyo')) {
			return 'generic_android_ver2_2';
		}
		
		$android_version = self::getAndroidVersion($this->userAgent);
		$version_string = str_replace('.', '_', $android_version);
		$deviceID = 'generic_android_ver'.$version_string;
		
		// Handle exceptions to the WURFL naming convention
		if ($deviceID == 'generic_android_ver2_0') $deviceID = 'generic_android_ver2';
		if ($deviceID == 'generic_android_ver4_0') $deviceID = 'generic_android_ver4';
		
		// Find a generic Android device ID
		if (($android_version < 3.0 || $android_version >= 4.0) 
				&& $this->userAgent->contains('Safari') 
				&& !$this->userAgent->contains('Mobile')) {
			// This is probably a tablet (Android 3.x is always a tablet, so it doesn't have a "_tablet" ID)
			if (in_array($deviceID.'_tablet', self::$constantIDs)) {
				return $deviceID.'_tablet';
			}
			return 'generic_android_ver1_5_tablet';
		}
		if (in_array($deviceID, self::$constantIDs)) {
			return $deviceID;
		}
		
		return 'generic_android';
	}
	
	
	/********* Android Utility Functions ***********/
	const ANDROID_DEFAULT_VERSION = 2.0;
	
	public static $validAndroidVersions = array('1.0', '1.5', '1.6', '2.0', '2.1', '2.2', '2.3', '2.4', '3.0', '3.1', '3.2', '3.3', '4.0', '4.1', '4.2', '4.3', '4.4', '4.5', '5.0', '5.1', '5.2', '5.3', '6.0', '6.1');
	public static $androidReleaseMap = array(
		'Cupcake' => '1.5',
		'Donut' => '1.6',
		'Eclair' => '2.1',
		'Froyo' => '2.2',
		'Gingerbread' => '2.3',
		'Honeycomb' => '3.0',
		'Ice Cream Sandwich' => '4.0',
		'Jelly Bean' => '4.1', // Note: 4.2/4.3 is also Jelly Bean
		'KitKat' => '4.4',
	);
	
	/**
	 * Get the Android version from the User Agent, or the default Android version is it cannot be determined
	 * @param string $ua User Agent
	 * @param boolean $use_default Return the default version on fail, else return null
	 * @return string Android version
	 * @see self::ANDROID_DEFAULT_VERSION
	 */
	public static function getAndroidVersion($ua, $use_default=true) {
		// Replace Android version names with their numbers
		// ex: Froyo => 2.2
		$ua = str_replace(array_keys(self::$androidReleaseMap), array_values(self::$androidReleaseMap), $ua);
		
		// Initializing $version
		$version = null;
		
		// Look for "Android <Version>" first and then for "Android/<Version>"
		if (preg_match('#Android (\d\.\d)#', $ua, $matches)) {
			$version = $matches[1];
		} else if (preg_match('#Android/(\d\.\d)#', $ua, $matches)) {
			$version = $matches[1];
		}
		
		// Now check extracted Android version for validity
		if (in_array($version, self::$validAndroidVersions)) {
			return $version;
		}
		return $use_default? self::ANDROID_DEFAULT_VERSION: null;
	}
	
	/**
	 * Get the model name from the provided user agent or null if it cannot be determined
	 * @param string $ua
	 * @param string $use_default
	 * @return NULL|string
	 */
	public static function getAndroidModel($ua, $use_default=true) {
		// Normalize spaces in UA before capturing parts
		$ua = preg_replace('|;(?! )|', '; ', $ua);
		// Different logic for Mozillite and non-Mozillite UAs to isolate model name
		// Non-Mozillite UAs get first preference
		if (preg_match('#(^[A-Za-z0-9_\-\+ ]+)[/ ]?(?:[A-Za-z0-9_\-\+\.]+)? +Linux/[0-9\.]+ +Android[ /][0-9\.]+ +Release/[0-9\.]+#', $ua, $matches)) {
			// Trim off spaces and semicolons
			$model = rtrim($matches[1], ' ;');
			
		// Locales are optional for matching model name since UAs like Chrome Mobile do not contain them
		} else if (preg_match('#Android [^;]+;(?>(?: xx-xx[ ;]+)?)(.+?)(?:Build/|\))#', $ua, $matches)) {
			// Trim off spaces and semicolons
			$model = rtrim($matches[1], ' ;');
			
		// Additional logic to capture model names in Amazon webview/appstore UAs
		} else if (preg_match('#^(?:AmazonWebView|Appstore|Amazon\.com)/.+Android[/ ][\d\.]+/(?:[\d]+/)?([A-Za-z0-9_\- ]+)\b#', $ua, $matches)) {
			$model = $matches[1];
		} else {
			return null;
		}
		
		
		// The previous RegEx may return just "Build/.*" for UAs like:
		// HTC_Dream Mozilla/5.0 (Linux; U; Android 1.5; xx-xx; Build/CUPCAKE) AppleWebKit/528.5+ (KHTML, like Gecko) Version/3.1.2 Mobile Safari/525.20.1
		if (strpos($model, 'Build/') === 0) {
			return null;
		}

		// Normalize Chinese UAs
		$model = preg_replace('#(?:_CMCC_TD|_CMCC|_TD)\b#', '', $model);
		
		// Normalize models with resolution
		if (strpos($model, '*') !== false) {
			if (($pos = strpos($model, '/')) !== false) {
				$model = substr($model, 0, $pos);
			}
		}
		
		// Normalize Huawei UAs
		$model = str_replace('HW-HUAWEI_', 'HUAWEI ', $model);
		
		// Normalize Coolpad UAs
		if (strpos($model, 'YL-Coolpad') !== false) {
			$model = preg_replace('#YL-Coolpad[ _]#', 'Coolpad ', $model);
		}
		
		// HTC
		if (strpos($model, 'HTC') !== false) {
			// Normalize "HTC/"
			$model = preg_replace('#HTC[ _\-/]#', 'HTC~', $model);
			
			// Remove the version
			if (($pos = strpos($model, '/')) !== false) {
				$model = substr($model, 0, $pos);
			}
			$model = preg_replace('#( V| )\d+?\.[\d\.]+$#', '', $model);
			
		}
		
		// Samsung
		$model = preg_replace('#(SAMSUNG[^/]+)/.*$#', '$1', $model);
		
		// Orange
		$model = preg_replace('#ORANGE/.*$#', 'ORANGE', $model);
		
		// LG
		$model = preg_replace('#(LG-?[A-Za-z0-9\-]+).*$#', '$1', $model);
		
		// Serial Number
		$model = preg_replace('#\[[\d]{10}\]#', '', $model);
		
		// Remove whitespace
		$model = trim($model);
		
		// Normalize Samsung and Sony/SonyEricsson model name changes due to Chrome Mobile
		$model = preg_replace('#^(?:SAMSUNG|SonyEricsson|Sony)[ \-]?#', '', $model);
		
		return (strlen($model) === 0)? null: $model;
	}
	
}
