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
class PantechUserAgentMatcher extends UserAgentMatcher {
	
	public static function canHandle(TeraWurflHttpRequest $httpRequest) {
		if ($httpRequest->isDesktopBrowser()) return false;
		return $httpRequest->user_agent->startsWith(array('Pantech', 'PT-', 'PANTECH', 'PG-'));
	}
	
	public function applyConclusiveMatch() {
		if ($this->userAgent->startsWith('Pantech') || $this->userAgent->startsWith('PANTECH')) {
			$result = $this->ldMatch(5);
			if ($result !== WurflConstants::NO_MATCH) {
				return $result;
			}
		}
		return $this->risMatch($this->userAgent->firstSlash());
	}
}
