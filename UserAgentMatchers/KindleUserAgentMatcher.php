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
class KindleUserAgentMatcher extends UserAgentMatcher {
	
	public $runtime_normalization = true;
	
	public static $constantIDs = array(
		'amazon_kindle_ver1',
		'amazon_kindle2_ver1',
		'amazon_kindle3_ver1',
		'amazon_kindle_fire_ver1',
		'generic_amazon_android_kindle',
		'generic_amazon_kindle',
	);
	
	public static function canHandle(TeraWurflHttpRequest $httpRequest) {
		return $httpRequest->user_agent->contains(array('Kindle', 'Silk'));
	}
	
	public function applyConclusiveMatch() {
		$search = 'Kindle/';
		$idx = strpos($this->userAgent, $search);
		if ($idx !== false) {
			// Version/4.0 Kindle/3.0 (screen 600x800; rotate) Mozilla/5.0 (Linux; U; zh-cn.utf8) AppleWebKit/528.5+ (KHTML, like Gecko, Safari/528.5+)
			//        $idx ^      ^ $tolerance
			$tolerance = $idx + strlen($search) + 1;
			$kindle_version = $this->userAgent->normalized[$tolerance];
			// RIS match only Kindle/1-3
			if ($kindle_version >= 1 && $kindle_version <= 3) {
				return $this->risMatch($tolerance);
			}
		}
		if ($this->userAgent->contains('Android') && $this->userAgent->contains('Kindle Fire')) {
			$model = AndroidUserAgentMatcher::getAndroidModel($this->userAgent, false);
			$version = AndroidUserAgentMatcher::getAndroidVersion($this->userAgent, false);
			if ($model !== null && $version !== null) {
				$prefix = $version.' '.$model.WurflConstants::RIS_DELIMITER;
				$this->userAgent->set($prefix.$this->userAgent);
				return $this->risMatch(strlen($prefix));
			}
		}
		
/* TESTING - DO NOT PORT */
		$idx = strpos($this->userAgent, 'PlayStation');
		if ($idx !== false) {
			return $this->risMatch($this->userAgent->indexOfOrLength('.', $idx));
		}
/* END TESTING */
		
		return WurflConstants::NO_MATCH;
	}
	
	public function applyRecoveryMatch() {
		if ($this->userAgent->contains('Kindle/1')) return 'amazon_kindle_ver1';
		if ($this->userAgent->contains('Kindle/2')) return 'amazon_kindle2_ver1';
		if ($this->userAgent->contains('Kindle/3')) return 'amazon_kindle3_ver1';
		if ($this->userAgent->contains(array('Kindle Fire', 'Silk'))) return 'amazon_kindle_fire_ver1';
		return 'generic_amazon_kindle';
	}
}
