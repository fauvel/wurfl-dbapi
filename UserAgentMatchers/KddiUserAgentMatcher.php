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
class KddiUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array(
		'opwv_v62_generic'
	);
	
	public static function canHandle(TeraWurflHttpRequest $httpRequest) {
		if ($httpRequest->isDesktopBrowser()) return false;
		return $httpRequest->user_agent->contains('KDDI-');
	}
	
	public function applyConclusiveMatch() {
		if ($this->userAgent->startsWith('KDDI/')) {
			$tolerance = $this->userAgent->secondSlash();
		} else {
			$tolerance = $this->userAgent->firstSlash();
		}
		return $this->risMatch($tolerance);
	}
	
	public function applyRecoveryMatch() {
		return 'opwv_v62_generic';
	}
}
