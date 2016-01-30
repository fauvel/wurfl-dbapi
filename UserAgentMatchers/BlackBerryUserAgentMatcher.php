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
class BlackBerryUserAgentMatcher extends UserAgentMatcher {
	
	public static $constantIDs = array(
		'2.' => 'blackberry_generic_ver2',
		'3.2' => 'blackberry_generic_ver3_sub2',
		'3.3' => 'blackberry_generic_ver3_sub30',
		'3.5' => 'blackberry_generic_ver3_sub50',
		'3.6' => 'blackberry_generic_ver3_sub60',
		'3.7' => 'blackberry_generic_ver3_sub70',
		'4.1' => 'blackberry_generic_ver4_sub10',
		'4.2' => 'blackberry_generic_ver4_sub20',
		'4.3' => 'blackberry_generic_ver4_sub30',
		'4.5' => 'blackberry_generic_ver4_sub50',
		'4.6' => 'blackberry_generic_ver4_sub60',
		'4.7' => 'blackberry_generic_ver4_sub70',
		'4.' => 'blackberry_generic_ver4',
		'5.' => 'blackberry_generic_ver5',
		'6.' => 'blackberry_generic_ver6',
	
		'10' => 'blackberry_generic_ver10',
		'10t' => 'blackberry_generic_ver10_tablet',
	);
	
	public static function canHandle(TeraWurflHttpRequest $httpRequest) {
		if ($httpRequest->isDesktopBrowser()) return false;
		return ($httpRequest->user_agent->iContains('blackberry') || $httpRequest->user_agent->contains('(BB10;') || $httpRequest->user_agent->contains('(PlayBook'));
	}
	
	public function applyConclusiveMatch() {
		if ($this->userAgent->contains('BB10')) {
			$tolerance = $this->userAgent->indexOfOrLength(')');
		} else if ($this->userAgent->startsWith('Mozilla/4')) {
			$tolerance = $this->userAgent->secondSlash();
		} else if ($this->userAgent->startsWith('Mozilla/5')) {
			$tolerance = $this->userAgent->ordinalIndexOf(';', 3);
		} else if ($this->userAgent->contains('PlayBook')) {
			$tolerance = $this->userAgent->firstCloseParen();
		} else {
			$tolerance = $this->userAgent->firstSlash();
		}
		return $this->risMatch($tolerance);
	}
	
	public function applyRecoveryMatch() {
		// BlackBerry 10
		if ($this->userAgent->contains('BB10')) {
			if ($this->userAgent->contains('Mobile')) {
				return 'blackberry_generic_ver10';
			} else {
				return 'blackberry_generic_ver10_tablet';
			}
		} elseif ($this->userAgent->contains('PlayBook')) {
            return "rim_playbook_ver1";
		} else if (preg_match('#Black[Bb]erry[^/\s]+/(\d.\d)#', $this->userAgent, $matches)) {
			$version = $matches[1];
			foreach (self::$constantIDs as $vercode => $deviceID) {
				if (strpos($version, $vercode) !== false) {
					return $deviceID;
				}
			}
		}
		return WurflConstants::NO_MATCH;
	}
}
