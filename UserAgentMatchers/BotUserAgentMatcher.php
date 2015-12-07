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
class BotUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array(
	    'google_image_proxy',
	);
	
	public static function canHandle(TeraWurflHttpRequest $httpRequest) {
		return $httpRequest->isRobot();
	}
	
	public function applyConclusiveMatch() {
		
		if ($this->userAgent->contains("GoogleImageProxy")) {
			return 'google_image_proxy';
		}
		if ($this->userAgent->startsWith("Mozilla")) {
            $tolerance = $this->userAgent->firstCloseParen() + 1;
			return $this->risMatch($tolerance);
		}
		return $this->risMatch($this->userAgent->firstSlash());
	}
	
	public function applyRecoveryMatch() {
		return WurflConstants::GENERIC_WEB_BROWSER;
	}
}
