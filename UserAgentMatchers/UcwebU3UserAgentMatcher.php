<?php
/**
 * Copyright (c) 2014 ScientiaMobile, Inc.
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
class UcwebU3UserAgentMatcher extends UserAgentMatcher {

	/**
	 * This flag tells the WurflLoader that the User Agent may be permanantly
	 * altered during matching
	 * @var boolean
	 */
	public $runtime_normalization = true;

	public static $constantIDs = array(
		'generic_ucweb',
		
		'generic_ucweb_android_ver1',
		'generic_ucweb_android_ver2',
		'generic_ucweb_android_ver3',
		'generic_ucweb_android_ver4',
		'generic_ucweb_android_ver5',
		
		'apple_iphone_ver1_subuaucweb',
		'apple_iphone_ver2_subuaucweb',
		'apple_iphone_ver3_subuaucweb',
		'apple_iphone_ver4_subuaucweb',
		'apple_iphone_ver5_subuaucweb',
		'apple_iphone_ver6_subuaucweb',
		'apple_iphone_ver7_subuaucweb',
		
		'apple_ipad_ver1_subuaucweb',
		'apple_ipad_ver1_sub4_subuaucweb',
		'apple_ipad_ver1_sub5_subuaucweb',
		'apple_ipad_ver1_sub6_subuaucweb',
		'apple_ipad_ver1_sub7_subuaucweb',
	);

	public static function canHandle(TeraWurflHttpRequest $httpRequest) {
		if ($httpRequest->isDesktopBrowser()) return false;
		return ($httpRequest->user_agent->startsWith('Mozilla') && $httpRequest->user_agent->contains('UCBrowser'));
	}

	public function applyConclusiveMatch() {
		$ucb_version = self::getUcBrowserVersion($this->userAgent, false);
		if ($ucb_version === null) {
			return WurflConstants::NO_MATCH;
		}

		// Android U3K Mobile + Tablet
		if ($this->userAgent->contains('Android')) {
			// Apply Version+Model--- matching normalization
	
			$model = AndroidUserAgentMatcher::getAndroidModel($this->userAgent, false);
			$version = AndroidUserAgentMatcher::getAndroidVersion($this->userAgent, false);
			if ($model !== null && $version !== null) {
				$prefix = "$version U3Android $ucb_version $model".WurflConstants::RIS_DELIMITER;
				$this->userAgent->set($prefix.$this->userAgent);
				return $this->risMatch(strlen($prefix));
			}
		}
	
		// iPhone U3K
		else if ($this->userAgent->contains('iPhone;')) {
	
			if (preg_match('/iPhone OS (\d+)(?:_(\d+))?(?:_\d+)* like/', $this->userAgent, $matches)) {
				$version = $matches[1].'.'.$matches[2];
				$prefix = "$version U3iPhone $ucb_version".WurflConstants::RIS_DELIMITER;
				$this->userAgent->set($prefix.$this->userAgent);
				return $this->risMatch(strlen($prefix));
			}
		}
			
		// iPad U3K
		else if ($this->userAgent->contains('iPad')) {
			
			if (preg_match('/CPU OS (\d)_?(\d)?.+like Mac.+; iPad([0-9,]+)\) AppleWebKit/', $this->userAgent, $matches)) {
				$version = $matches[1].'.'.$matches[2];
				$model = $matches[3];
				$prefix = "$version U3iPad $ucb_version $model".WurflConstants::RIS_DELIMITER;
				$this->userAgent->set($prefix.$this->userAgent);
				return $this->risMatch(strlen($prefix));
			}
		}
				
		return WurflConstants::NO_MATCH;
	}


	public function applyRecoveryMatch() {
		// Android U3K Mobile + Tablet. This will also handle UCWEB7 recovery and point it to the UCWEB generic IDs.
		if ($this->userAgent->contains('Android')) {
			// Apply Version+Model--- matching normalization
			$version = AndroidUserAgentMatcher::getAndroidVersion($this->userAgent, false);
			$significant_version = explode('.', $version);
			if ($significant_version[0] !== null) {
				$deviceID = 'generic_ucweb_android_ver'.$significant_version[0];
				if (in_array($deviceID, self::$constantIDs)) {
					return $deviceID;
				}
			}
				
			return 'generic_ucweb_android_ver1';
		}

		// iPhone U3K
		else if ($this->userAgent->contains('iPhone;')) {
			if (preg_match('/iPhone OS (\d+)(?:_\d+)?.+ like/', $this->userAgent, $matches)) {
				$significant_version = $matches[1];
				$deviceID = 'apple_iphone_ver'.$significant_version.'_subuaucweb';
				if (in_array($deviceID, self::$constantIDs)) {
					return $deviceID;
				}
			}
				
			return 'apple_iphone_ver1_subuaucweb';
		}


		// iPad U3K
		else if ($this->userAgent->contains('iPad')) {
				
			if (preg_match('/CPU OS (\d+)(?:_\d+)?.+like Mac/', $this->userAgent, $matches)) {
				$significant_version = $matches[1];
				$deviceID = 'apple_ipad_ver1_sub'.$significant_version.'_subuaucweb';
				if (in_array($deviceID, self::$constantIDs)) {
					return $deviceID;
				}
			}
				
			return 'apple_ipad_ver1_subuaucweb';
		}
			
		return 'generic_ucweb';

	}
	
	public static function getUcBrowserVersion($ua, $use_default=true) {
		if (preg_match('/UCBrowser\/(\d+)\.\d/', $ua, $matches)) {
			$uc_version = $matches[1];
			return $uc_version;
		}
		return null;

	}
	
	public static function getUcAndroidVersion($ua, $use_default=true) {
		if (preg_match('/; Adr (\d+\.\d+)\.?/', $ua, $matches)) {
			$u2k_an_version = $matches[1];
			if (in_array($u2k_an_version, AndroidUserAgentMatcher::$validAndroidVersions)) {
				return $u2k_an_version;
			}

		}
		return $use_default? AndroidUserAgentMatcher::ANDROID_DEFAULT_VERSION: null;
	}
	
	// Slightly modified from Android's get model function
	public static function getUcAndroidModel($ua, $use_default=true) {
		// Locales are optional for matching model name since UAs like Chrome Mobile do not contain them
		if (!preg_match('#Adr [\d\.]+; [a-zA-Z]+-[a-zA-Z]+; (.*)\) U2#', $ua, $matches)) {
			return null;
		}
		
		$model = $matches[1];

		// HTC
		if (strpos($model, 'HTC') !== false) {
			// Normalize "HTC/"
			$model = preg_replace('#HTC[ _\-/]#', 'HTC~', $model);
			// Remove the version
			$model = preg_replace('#(/| +V?\d)[\.\d]+$#', '', $model);
			$model = preg_replace('#/.*$#', '', $model);
		}
		// Samsung
		$model = preg_replace('#(SAMSUNG[^/]+)/.*$#', '$1', $model);
		// Orange
		$model = preg_replace('#ORANGE/.*$#', 'ORANGE', $model);
		// LG
		$model = preg_replace('#(LG-[A-Za-z0-9\-]+).*$#', '$1', $model);
		// Serial Number
		$model = preg_replace('#\[[\d]{10}\]#', '', $model);

		$model = trim($model);
		return (strlen($model) == 0)? null: $model;
	}

}
