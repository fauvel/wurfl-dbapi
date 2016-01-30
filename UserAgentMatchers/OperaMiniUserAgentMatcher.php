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
class OperaMiniUserAgentMatcher extends UserAgentMatcher {
    
    /**
     * This flag tells the WurflLoader that the User Agent may be permanantly
     * altered during matching
     * @var boolean
     */
    public $runtime_normalization = true;
	
	public static $constantIDs = array(
		'Opera Mini/1' => 'generic_opera_mini_version1',
		'Opera Mini/2' => 'generic_opera_mini_version2',
		'Opera Mini/3' => 'generic_opera_mini_version3',
		'Opera Mini/4' => 'generic_opera_mini_version4',
		'Opera Mini/5' => 'generic_opera_mini_version5',
		'Opera Mini/6' => 'generic_opera_mini_version6',
		'Opera Mini/7' => 'generic_opera_mini_version7',
	);
	
	public static function canHandle(TeraWurflHttpRequest $httpRequest) {
		if ($httpRequest->isDesktopBrowser()) return false;
		return $httpRequest->user_agent->contains(array('Opera Mini', 'OperaMini', 'Opera Mobi', 'OperaMobi'));
	}
	
	public function applyConclusiveMatch() {
	    $model = self::getOperaModel($this->userAgent, false);
    
	    if ($model !== null) {
	        $prefix = $model.WurflConstants::RIS_DELIMITER;
	        $this->userAgent->set($prefix.$this->userAgent);
	        return $this->risMatch(strlen($prefix));
	    }
	    $opera_mini_idx = $this->userAgent->indexOf('Opera Mini');
		if ($opera_mini_idx !== false) {
			// Match up to the first '.' after 'Opera Mini'
			$tolerance = $this->userAgent->indexOf('.', $opera_mini_idx);
			if ($tolerance !== false) {
				// +1 to match just after the '.'
				return $this->risMatch($tolerance + 1);
			}
		}
		return $this->risMatch($this->userAgent->firstSlash());
	}
	
	public function applyRecoveryMatch() {
		foreach (self::$constantIDs as $keyword => $device_id) {
			if ($this->userAgent->contains($keyword)) {
				return $device_id;
			}
		}
		if ($this->userAgent->contains('Opera Mobi')) {
			return 'generic_opera_mini_version4';
		}
		return 'generic_opera_mini_version1';
	}
	
	/**
	 * Get the model name from the provided user agent or null if it cannot be determined
	 * @param string $ua
	 * @param string $use_default
	 * @return NULL|string
	 */
	public static function getOperaModel($ua, $use_default=true) {
	    if (preg_match('#^Opera/[\d\.]+ .+?\d{3}X\d{3} (.+)$#', $ua, $matches)) {
	        return $matches[1];
	    }
	}
}
