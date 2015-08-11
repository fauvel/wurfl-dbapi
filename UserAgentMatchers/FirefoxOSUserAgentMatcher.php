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
class FirefoxOSUserAgentMatcher extends UserAgentMatcher {

	public static $constantIDs = array(
		'generic_firefox_os',
		'firefox_os_ver1',
		'firefox_os_ver1_1',
		'firefox_os_ver1_2',
		'firefox_os_ver1_3',
		'firefox_os_ver1_3_tablet',
		'firefox_os_ver1_4',
		'firefox_os_ver1_4_tablet',
		'firefox_os_ver2_0',
		'firefox_os_ver2_0_tablet',
		'firefox_os_ver2_1',
		'firefox_os_ver2_1_tablet',
		'firefox_os_ver2_2',
		'firefox_os_ver2_2_tablet',
	);

	public static $firefoxOSMap = array(
		'18.0' => '1.0',
		'18.1' => '1.1',
		'26.0' => '1.2',
		'28.0' => '1.3',
		'30.0' => '1.4',
		'32.0' => '2.0',
		'33.0' => '2.1',
		'34.0' => '2.1',
		'37.0' => '2.2',
	);

	public static function canHandle(TeraWurflHttpRequest $httpRequest) {
		return ($httpRequest->user_agent->contains('Firefox/') && $httpRequest->user_agent->contains(array('Mobile', 'Tablet')));
	}

	public function applyConclusiveMatch() {
		// Mozilla/5.0 (Mobile; rv:18.0) Gecko/18.0 Firefox/18.0
		// Mozilla/5.0 (Mobile; ZTEOPEN; rv:18.1) Gecko/18.1 Firefox/18.1
		// Mozilla/5.0 (Tablet; rv:26.0) Gecko/26.0 Firefox/26.0
		if (preg_match('#\brv:\d+\.\d+(.)#', $this->userAgent, $matches, PREG_OFFSET_CAPTURE)) {
			$tolerance = $matches[1][1] + 1;
			return $this->risMatch($tolerance);
		}
		return WurflConstants::NO_MATCH;
	}

	public function applyRecoveryMatch() {

		$version_string = str_replace('.', '_', self::getFirefoxOSVersion($this->userAgent));

		// Replace X_0 to X because the WURFL IDs are of the type "firefox_os_verX" and not "firefox_os_verX_0"
		$version_string = str_replace('_0', '', $version_string);

		$deviceID = 'firefox_os_ver'.$version_string;

		// Tablet specific recovery logic
		if (strpos($this->userAgent, 'Tablet') !== false) {
			if (in_array($deviceID.'_tablet', self::$constantIDs)) {
				return $deviceID.'_tablet';
			}
			return 'firefox_os_ver1_3_tablet';

		}

		if (in_array($deviceID, self::$constantIDs)) {
			return $deviceID;
		}

		return 'generic_firefox_os';
	}

	// Function to extract Firefox OS version from Gecko/Firefox Browser version in the User-Agent
	public static function getFirefoxOSVersion($ua) {

		// Find Firefox Browser/Gecko version
		if (preg_match('#\brv:(\d+\.\d+)#', $ua, $matches) && array_key_exists($matches[1], self::$firefoxOSMap)) {
			return self::$firefoxOSMap[$matches[1]];
		}

		if (strpos($ua, 'Tablet') !== false) {
			// Firefox OS 1.3 is the lowest version of Firefox OS to have a tablet WURFL ID
			return "1.3";
		}

		return "1.0";

	}
}