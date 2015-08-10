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
 * @package	WURFL_UserAgentMatcher
 * @copyright  ScientiaMobile, Inc.
 * @author	 Steve Kamerman <steve AT scientiamobile.com>
 * @license	GNU Affero General Public License
 * @version	$id$
 */
/**
 * Provides a specific user agent matching technique
 * @package TeraWurflUserAgentMatchers
 */
class DesktopApplicationUserAgentMatcher extends UserAgentMatcher {
		
		public $runtime_normalization = true;
		
		public static $constantIDs = array(
			'generic_desktop_application',
			'mozilla_thunderbird',
			'ms_outlook',
			'ms_outlook_subua14',
			'ms_outlook_subua15',
			'ms_office',
			'ms_office_subua12',
			'ms_office_subua14',
			'ms_office_subua15',
		
		);
		
		public static function canHandle(TeraWurflHttpRequest $httpRequest) {
			if ($httpRequest->isMobileBrowser()) return false;
			return $httpRequest->user_agent->contains(array('Thunderbird', 'Microsoft Outlook', 'MSOffice'));
		}
		
		public function applyConclusiveMatch() {
			if ($this->userAgent->contains('Thunderbird')) {
				$this->userAgent->set(substr($this->userAgent, $this->userAgent->indexOf('Thunderbird')));
				$idx = $this->userAgent->indexOf('.');
				if ($idx !== false) {
					return $this->risMatch($idx + 1);
				}
			}
			
			// Check for Outlook before Office
			if (preg_match('#Microsoft Outlook ([0-9]+)\.#', $this->userAgent, $matches)) {
				$deviceID = 'ms_outlook_subua'.$matches[1];
				if (in_array($deviceID, self::$constantIDs)) {
					return $deviceID;
				}
				
			} else if (preg_match('#MSOffice ([0-9]+)\b#', $this->userAgent, $matches)) {
				$deviceID = 'ms_office_subua'.$matches[1];
				if (in_array($deviceID, self::$constantIDs)) {
					return $deviceID;
				}
			}
			
			return WurflConstants::NO_MATCH;
		}
		
		public function applyRecoveryMatch() {
			if ($this->userAgent->contains('Thunderbird')) {
				return 'mozilla_thunderbird';
			}
			
			else if ($this->userAgent->contains('Microsoft Outlook')) {
				return 'ms_outlook';
			}
			
			else if ($this->userAgent->contains('MSOffice')) {
				return 'ms_office';
			}
			
			return WurflConstants::GENERIC_WEB_BROWSER;
			
		}
}