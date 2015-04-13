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
class UbuntuTouchOSUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array(
		'generic_ubuntu_touch_os',
		'generic_ubuntu_touch_os_tablet',
		
	);
	
	public static function canHandle(TeraWurflHttpRequest $httpRequest) {
		return ($httpRequest->user_agent->contains('Ubuntu') && $httpRequest->user_agent->contains(array('Mobile', 'Tablet')));
	}
	
	public function applyConclusiveMatch() {
		// Mozilla/5.0 (Ubuntu; Mobile) WebKit/537.21
		// Mozilla/5.0 (Ubuntu; Tablet) WebKit/537.21
		//									  ^ RIS tolerance
		// Mozilla/5.0 (Linux; Ubuntu 14.04 like Android 4.4) AppleWebKit/537.36 Chromium/35.0.1870.2 Mobile Safari/537.36
		//											   ^ RIS tolerance
		
		if ($this->userAgent->contains("like Android")) {
			$search = 'like Android';
		} else {
			$search = 'WebKit/';
		}	
		$idx = strpos($this->userAgent, $search);
			if ($idx !== false) {
				// Match to the end of the search string
				return $this->risMatch($idx + strlen($search));
			}
		return WurflConstants::NO_MATCH;
	}
	
	public function applyRecoveryMatch() {

		if ($this->userAgent->contains("Tablet")) {
			return 'generic_ubuntu_touch_os_tablet';	
		}
		else {
			return 'generic_ubuntu_touch_os';		
		}

	}
}