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
class CatchAllMozillaUserAgentMatcher extends UserAgentMatcher {

    public $matcher;
    public $match_type;
    public $match = false;

    public function __construct(TeraWurfl $wurfl) {
        parent::__construct($wurfl);
        $this->matcher = $this->matcherName();
    }

    public static function canHandle(TeraWurflHttpRequest $httpRequest) {
        return $httpRequest->user_agent->startsWith(array('Mozilla/4', 'Mozilla/5'));
    }

    public function applyConclusiveMatch() {
        $this->match_type = 'conclusive';

        if (TeraWurflConfig::$SIMPLE_DESKTOP_ENGINE_ENABLE === true) {
            //High performance mode
            $tolerance = $this->userAgent->firstCloseParen() + 1;
            return $this->risMatch($tolerance);
        } else {
            //High accuracy mode
            $deviceID = $this->ldMatch(5);
            if ($deviceID != WurflConstants::NO_MATCH) $this->match = true;
            return $deviceID;
        }
    }
}
