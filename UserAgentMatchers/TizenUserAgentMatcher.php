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
class TizenUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array(
		'generic_tizen',
		'generic_tizen_ver1_0',
		'generic_tizen_ver2_0',
		'generic_tizen_ver2_1',
		'generic_tizen_ver2_2',
	);
	
	public static function canHandle(TeraWurflHttpRequest $httpRequest) {
		return ($httpRequest->user_agent->startsWith("Mozilla") && $httpRequest->user_agent->contains("Tizen"));
	}
	
	public function applyConclusiveMatch() {

		// Mozilla/5.0 (Linux; Tizen 2.2; SAMSUNG SM-Z910F) AppleWebKit/537.3 (KHTML, like Gecko) Version/2.2 Mobile Safari/537.3
		//															   ^ RIS tolerance
		
		$search = 'AppleWebKit/';
		$idx = strpos($this->userAgent, $search);
		if ($idx !== false) {
			// Match to the end of the search string
			return $this->risMatch($idx + strlen($search));
		}
		return WurflConstants::NO_MATCH;
	}
	
	public function applyRecoveryMatch() {
	
		$version = self::getTizenVersion($this->userAgent); 
		$version = "generic_tizen_ver".str_replace(".", "_", $version);
		if (in_array($version, self::$constantIDs)) {
			return $version;
		}
		return "generic_tizen";
	}
	
	public static $validTizenVersions = array('1.0', '2.0', '2.1', '2.2');
	
	public static function getTizenVersion($ua) {
		
		// Find Tizen version
		if (preg_match('#Tizen (\d+?\.\d+?)#', $ua, $matches) && in_array($matches[1], self::$validTizenVersions)) {				
			return $matches[1];
		}
		//Default		
		return "1.0";
	}
}
