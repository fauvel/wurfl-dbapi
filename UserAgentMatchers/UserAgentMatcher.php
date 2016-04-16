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
 * An abstract class that all UserAgentMatchers must extend.
 * @package TeraWurflUserAgentMatchers
 */
abstract class UserAgentMatcher {
	
	/**
	 * @var TeraWurfl Running instance of Tera-WURFL
	 */
	protected $wurfl;
	/**
	 * @var TeraWurflUserAgent
	 */
	protected $userAgent;
	/**
	 * WURFL IDs that are hardcoded in this connector.  Used for compatibility testing against new WURFLs
	 * @var array
	 */
	public static $constantIDs = array();
	/**
	 * @var Array List of WURFL IDs => User Agents.  Typically used for matching user agents.
	 */
	public $deviceList;
	/**
	 * If true, the matcher will not perform any RIS or LD matching
	 * @var boolean
	 */
	public $simulation = false;
	/**
	 * Set to true if this UserAgentMatcher is allowed to permenantly modify the User Agent while matching 
	 * @var boolean
	 */
	public $runtime_normalization = false;
    /**
     * Instantiates a new UserAgentMatcher
     * @param TeraWurfl $wurfl
     */
	public function __construct(TeraWurfl $wurfl) {
		$this->wurfl = $wurfl;
		$this->userAgent = $this->wurfl->httpRequest->user_agent;
	}

    /**
     * Attempts to find a conclusively matching WURFL ID
     * @return string Matching WURFL ID
     */
    abstract public function applyConclusiveMatch();
    
    /**
     * Attempts to find a loosely matching WURFL ID
     * @return string Matching WURFL ID
     */
    public function applyRecoveryMatch() {
        // At this point, a recovery match is really no match at all.
        $this->match_type = 'none';
        $this->match = false;
        if (TeraWurflConfig::$SIMPLE_DESKTOP_ENGINE_ENABLE === false &&
            SimpleDesktopUserAgentMatcher::isDesktopBrowserHeavyDutyAnalysis($this->wurfl->httpRequest)) return WurflConstants::GENERIC_WEB_BROWSER;

        if ($this->userAgent->contains('CoreMedia')) return 'apple_iphone_coremedia_ver1';

        if ($this->userAgent->contains('Windows CE')) return 'generic_ms_mobile';

        if ($this->userAgent->contains('UP.Browser/7.2')) return 'opwv_v72_generic';
        if ($this->userAgent->contains('UP.Browser/7'))   return 'opwv_v7_generic';
        if ($this->userAgent->contains('UP.Browser/6.2')) return 'opwv_v62_generic';
        if ($this->userAgent->contains('UP.Browser/6'))   return 'opwv_v6_generic';
        if ($this->userAgent->contains('UP.Browser/5'))   return 'upgui_generic';
        if ($this->userAgent->contains('UP.Browser/4'))   return 'uptext_generic';
        if ($this->userAgent->contains('UP.Browser/3'))   return 'uptext_generic';

        // Series 60
        if ($this->userAgent->contains('Series60')) return 'nokia_generic_series60';

        // Access/Net Front
        if ($this->userAgent->contains(array('NetFront/3.0', 'ACS-NF/3.0'))) return 'generic_netfront_ver3';
        if ($this->userAgent->contains(array('NetFront/3.1', 'ACS-NF/3.1'))) return 'generic_netfront_ver3_1';
        if ($this->userAgent->contains(array('NetFront/3.2', 'ACS-NF/3.2'))) return 'generic_netfront_ver3_2';
        if ($this->userAgent->contains(array('NetFront/3.3', 'ACS-NF/3.3'))) return 'generic_netfront_ver3_3';
        if ($this->userAgent->contains('NetFront/3.4')) return 'generic_netfront_ver3_4';
        if ($this->userAgent->contains('NetFront/3.5')) return 'generic_netfront_ver3_5';
        if ($this->userAgent->contains('NetFront/4.0')) return 'generic_netfront_ver4_0';

        // Contains Mozilla/, but not at the beginning of the UA
        // ie: MOTORAZR V8/R601_G_80.41.17R Mozilla/4.0 (compatible; MSIE 6.0 Linux; MOTORAZR V88.50) Profile/MIDP-2.0 Configuration/CLDC-1.1 Opera 8.50[zh]
        if ($this->userAgent->indexOf('Mozilla/') > 0) return WurflConstants::GENERIC_XHTML;

        if ($this->userAgent->contains(array('Obigo','AU-MIC/2','AU-MIC-','AU-OBIGO/', 'Teleca Q03B1'))) {
            return WurflConstants::GENERIC_XHTML;
        }

        // DoCoMo
        if ($this->userAgent->startsWith(array('DoCoMo', 'KDDI'))) return 'docomo_generic_jap_ver1';
        return WurflConstants::NO_MATCH;
    }
    
    /**
     * Returns true if this Matcher can handle the given $httpRequest
     * @param TeraWurflHttpRequest $httpRequest
     * @return boolean
     */
    public static function canHandle(TeraWurflHttpRequest $httpRequest) {
    	return true;
    }
    
    /**
     * Updates the deviceList Array to contain all the WURFL IDs that are related to the current UserAgentMatcher
     */
    protected function updateDeviceList() {
    	if (is_array($this->deviceList) && count($this->deviceList)>0) return;
    	$this->deviceList = $this->wurfl->db->getFullDeviceList($this->wurfl->fullTableName());
    }
    /**
     * Attempts to match given user agent string to a device from the database by comparing less and less of the strings until a match is found (RIS, Reduction in String)
     * @param int $tolerance Tolerance, how many characters must match from left to right
     * @return string WURFL ID
     */
    public function risMatch($tolerance) {
        if ($tolerance === null) return WurflConstants::NO_MATCH;
    	if ($this->simulation) return WurflConstants::NO_MATCH;
    	if ($this->wurfl->db->db_implements_ris) {
    		return $this->wurfl->db->getDeviceFromUA_RIS($this->userAgent->normalized, $tolerance, $this);
    	}
    	$this->updateDeviceList();
    	return UserAgentUtils::risMatch($this->userAgent->normalized, $tolerance, $this);
    }
    
    /**
     * Uses RIS to match the given User Agent $prefix, using the string length of the $prefix as the tolerance.  Returns device ID $default if a match is not found.
     * @param string $prefix The substring of the desired user agent, ex: "Mozilla/5"
     * @param string $default This device ID will be returned if the match fails
     */
    public function risMatchUAPrefix($prefix, $default=WurflConstants::NO_MATCH) {
    	$tolerance = strlen($prefix);
    	$deviceID = $this->risMatch($tolerance);
    	if ($deviceID == WurflConstants::NO_MATCH) {
    		return $default;
    	}
    	return $deviceID;
    }
    /**
     * Returns the name of the UserAgentMatcher in use
     * @return string UserAgentMatcher name
     */
    public function matcherName() {
    	return get_class($this);
    }
    /**
     * Returns the database table suffix for the current UserAgentMatcher
     * @return string Table suffix
     */
    public function tableSuffix() {
    	$cname = $this->matcherName();
    	return substr($cname, 0, strpos($cname, 'UserAgentMatcher'));
    }
    
    public static function getRequiredDeviceIDs() {
    	$ids = array();
    	foreach(WurflConstants::$matchers as $matcher) {
    		$matcherClass = $matcher."UserAgentMatcher";
    		$file = dirname(__FILE__)."/{$matcherClass}.php";
    		require_once($file);
    		$properties = get_class_vars($matcherClass);
    		$ids = array_merge($ids,$properties['constantIDs']);
    	}
    	return array_unique($ids);
    }
}
