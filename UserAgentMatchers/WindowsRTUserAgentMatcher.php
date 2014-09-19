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
class WindowsRTUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array(
		'generic_windows_8_rt',
		'windows_8_rt_ver1_subos81',
	);
	
	public static function canHandle(TeraWurflHttpRequest $httpRequest) {
		return $httpRequest->user_agent->contains('Windows NT ') && $httpRequest->user_agent->contains(' ARM;') && $httpRequest->user_agent->contains('Trident/');
	}
	
	public function applyConclusiveMatch() {
		// Example Windows 8 RT MSIE 10 UA:
		// Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; ARM; Trident/6.0; Touch)
		//                                                        ^ RIS Tolerance
		//Example Windows 8.1 RT MSIE 11 UA
		//Mozilla/5.0 (Windows NT 6.3; ARM; Trident/7.0; Touch; rv:11.0) like Gecko
		//																    	  ^ RIS Tolerance
		
		if ($this->userAgent->contains("like Gecko")) {
			//Use this logic for MSIE 11 and above 
			$search = ' Gecko';
			$idx = strpos($this->userAgent, $search);
			if ($idx !== false) {
				// Match to the end of the search string
				return $this->risMatch($idx + strlen($search));
			}
		}
		else {
			$search = ' ARM;';
			$idx = strpos($this->userAgent, $search);
			if ($idx !== false) {
				// Match to the end of the search string
				return $this->risMatch($idx + strlen($search));
			}
		}
		return WurflConstants::NO_MATCH;
	}
	
	public function applyRecoveryMatch() {
		if ($this->userAgent->contains("like Gecko")) {
			return 'windows_8_rt_ver1_subos81';
		}
		else return 'generic_windows_8_rt';
	}
}
