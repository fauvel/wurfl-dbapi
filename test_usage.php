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
 * @package    WURFL
 * @copyright  ScientiaMobile, Inc.
 * @author     Steve Kamerman <steve AT scientiamobile.com>
 * @license    GNU Affero General Public License
 * @version    $id$
 */
// Include the Tera-WURFL file
require_once realpath(dirname(__FILE__).'/TeraWurfl.php');
// Instantiate the Tera-WURFL object
$wurflObj = new TeraWurfl();

if (PHP_SAPI == "cli") {
	// We're running from the command line

	// Get the capabilities from a user-agent
	$ua = "Mozilla/5.0 (Linux; Android 4.4.2; Nexus 5 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.166 Mobile Safari/537.36";
	$wurflObj->getDeviceCapabilitiesFromAgent($ua);

	// Print the capabilities array
	echo var_export($wurflObj->capabilities,true)."\n";

	// Print the virtual capabilities array
	echo var_export($wurflObj->getAllVirtualCapabilities(),true)."\n";

} else {
	// We're running from a web server

	// Get the capabilities from the object
	$wurflObj->getDeviceCapabilitiesFromRequest();

	// Print the capabilities array
	echo "<pre>".htmlspecialchars(var_export($wurflObj->capabilities,true))."</pre>";

	// Print the virtual capabilities array
	echo "<pre>".htmlspecialchars(var_export($wurflObj->getAllVirtualCapabilities(),true))."</pre>";
}