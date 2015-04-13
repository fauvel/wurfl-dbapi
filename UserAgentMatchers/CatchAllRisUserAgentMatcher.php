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
 * Provides a generic user agent matching technique
 * @package TeraWurflUserAgentMatchers
 */
class CatchAllRisUserAgentMatcher extends UserAgentMatcher {

    public $matcher;
    public $match_type;
    public $match = false;

    public function __construct(TeraWurfl $wurfl) {
        parent::__construct($wurfl);
        $this->matcher = $this->matcherName();
    }

    public static function canHandle(TeraWurflHttpRequest $httpRequest) {
        return true;
    }

    public function applyConclusiveMatch() {
        $this->match_type = 'conclusive';
        $deviceID = $this->risMatch($this->userAgent->firstSlash());
        if ($deviceID != WurflConstants::NO_MATCH) $this->match = true;
        return $deviceID;
    }
}
