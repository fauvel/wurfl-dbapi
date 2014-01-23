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
class FirefoxUserAgentMatcher extends UserAgentMatcher {
	
	public $runtime_normalization = true;
	
	public static $constantIDs = array(
		'firefox',
		'firefox_1',
		'firefox_2',
		'firefox_3',
		'firefox_4_0',
		'firefox_5_0',
		'firefox_6_0',
		'firefox_7_0',
		'firefox_8_0',
		'firefox_9_0',
		'firefox_10_0',
		'firefox_11_0',
		'firefox_12_0',
	);
	
	public static function canHandle(TeraWurflHttpRequest $httpRequest) {
		if ($httpRequest->isMobileBrowser()) return false;
		return ($httpRequest->user_agent->contains('Firefox') && !$httpRequest->user_agent->contains(array('Tablet', 'Sony', 'Novarra', 'Opera')));
	}
	
	public function applyConclusiveMatch() {
		$this->userAgent->set(substr($this->userAgent, $this->userAgent->indexOf('Firefox')));
		return $this->risMatch($this->userAgent->indexOf('.'));
	}
	
	public function applyRecoveryMatch() {
		$matches = array();
		if (preg_match('/Firefox\/(\d+)\.\d/', $this->userAgent, $matches)) {
			$firefox_version = $matches[1];
			if ((int)$firefox_version <= 3) {
				$id = 'firefox_'.$firefox_version;
			} else {
				$id = 'firefox_'.$firefox_version.'_0';
			}
			if (in_array($id, self::$constantIDs)) return $id;
		}
		return 'firefox';
	}
}