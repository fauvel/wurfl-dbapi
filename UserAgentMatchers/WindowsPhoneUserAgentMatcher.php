<?php
/**
 * Copyright (c) 2011 ScientiaMobile, Inc.
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
class WindowsPhoneUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array(
		'generic_ms_winmo6_5',
		'generic_ms_phone_os7',
		'generic_ms_phone_os7_5',
	);
	
	public static function canHandle(TeraWurflHttpRequest $httpRequest) {
		if ($httpRequest->isDesktopBrowser()) return false;
		return $httpRequest->user_agent->contains('Windows Phone');
	}
	
	public function applyConclusiveMatch() {
		// Exact and Recovery match only
		return WurflConstants::NO_MATCH;
	}
	public function applyRecoveryMatch() {
		if ($this->userAgent->contains('Windows Phone 6.5')) return 'generic_ms_winmo6_5';
		if ($this->userAgent->contains('Windows Phone OS 7.0')) return 'generic_ms_phone_os7';
		if ($this->userAgent->contains('Windows Phone OS 7.5')) return 'generic_ms_phone_os7_5';
		return WurflConstants::NO_MATCH;
	}
}