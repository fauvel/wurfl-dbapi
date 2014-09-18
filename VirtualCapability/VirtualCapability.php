<?php
/**
 * Copyright (c) 2014 ScientiaMobile, Inc.
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
/**
 * Defines the virtual capabilities
 * @package TeraWurflVirtualCapability
 */
abstract class VirtualCapability {
	
	protected $required_capabilities = array();
	protected $use_caching = false;
	protected $cached_value;

	private static $loaded_capabilities;
	
	/**
	 * @var TeraWurfl
	 */
	protected $wurfl;
	
	public function __construct(TeraWurfl $wurfl) {
		$this->wurfl = $wurfl;
	}
	
	public function hasRequiredCapabilities() {
		if (empty($this->required_capabilities)) return true;
		if (self::$loaded_capabilities === null) {
			self::$loaded_capabilities = $this->wurfl->getLoadedCapabilityNames();
		}
		$missing_caps = array_diff($this->required_capabilities, self::$loaded_capabilities);
		return empty($missing_caps);
	}
	
	public function getRequiredCapabilities() {
		return $this->required_capabilities;
	}
	
	public function getValue() {
		return ($this->use_caching)? $this->computeCached(): $this->compute();
	}
	
	abstract protected function compute();
	
	private function computeCached() {
		if ($this->cached_value === null) {
			$this->cached_value = $this->compute();
		}
		return $this->cached_value;
	}
}

class VirtualCapability_IsAndroid extends VirtualCapability {
	
	protected $required_capabilities = array('device_os');
	
	protected function compute() {
		return ($this->wurfl->device_os == 'Android');
	}
}

class VirtualCapability_IsIos extends VirtualCapability {
	
	protected $required_capabilities = array('device_os');

	protected function compute() {
		return ($this->wurfl->device_os == 'iOS');
	}
}

class VirtualCapability_IsWindowsPhone extends VirtualCapability {

	protected $required_capabilities = array('device_os');

	protected function compute() {
		return ($this->wurfl->device_os == 'Windows Phone OS');
	}
}

class VirtualCapability_IsWmlPreferred extends VirtualCapability {

	protected $required_capabilities = array('xhtml_support_level');

	protected function compute() {
		return ($this->wurfl->xhtml_support_level <= 0);
	}
}

class VirtualCapability_IsXhtmlmpPreferred extends VirtualCapability {

	protected $required_capabilities = array(
		'xhtml_support_level',
		'preferred_markup',
	);

	protected function compute() {
		return ($this->wurfl->xhtml_support_level > 0 && strpos($this->wurfl->preferred_markup, 'html_web') !== 0);
	}
}
class VirtualCapability_IsHtmlPreferred extends VirtualCapability {

	protected $required_capabilities = array('preferred_markup');

	protected function compute() {
		return (strpos($this->wurfl->preferred_markup, 'html_web') === 0);
	}
}
class VirtualCapability_IsTouchscreen extends VirtualCapability {

	protected $required_capabilities = array('pointing_method');

	protected function compute() {
		return ($this->wurfl->pointing_method == 'touchscreen');
	}
}

class VirtualCapability_IsLargescreen extends VirtualCapability {

	protected $required_capabilities = array(
		'resolution_width',
		'resolution_height',
	);

	protected function compute() {
		return ($this->wurfl->resolution_width >= 480 && $this->wurfl->resolution_height >= 480);
	}
}

class VirtualCapability_IsApp extends VirtualCapability {
	
	protected $required_capabilities = array('device_os');
	
	/**
	 * Simple strings or regex patterns that indicate a UA is from a native app
	 * @var array
	 */
	protected $patterns = array(
		'^Dalvik',
		'Darwin/',
		'CFNetwork',
		'^Windows Phone Ad Client',
		'^NativeHost',
		'^AndroidDownloadManager',
		'-HttpClient',
		'^AppCake',
		'AppEngine-Google',
		'AppleCoreMedia',
		'^AppTrailers',
		'^ChoiceFM',
		'^ClassicFM',
		'^Clipfish',
		'^FaceFighter',
		'^Flixster',
		'^Gold/',
		'^GoogleAnalytics/',
		'^Heart/',
		'^iBrowser/',
		'iTunes-',
		'^Java/',
		'^LBC/3.',
		'Twitter',
		'Pinterest',
		'^Instagram',
		'FBAN',
		'#iP(hone|od|ad)[\d],[\d]#',
		// namespace notation (com.google.youtube)
		'#[a-z]{3,}(?:\.[a-z]+){2,}#',
		//Windows MSIE Webview
		'WebView',
	);
	
	protected function compute() {
		$ua = (string)$this->wurfl->httpRequest->user_agent->original;
		
		if ($this->wurfl->device_os == "iOS" && !$this->wurfl->httpRequest->user_agent->contains("Safari")) return true;
		foreach ($this->patterns as $pattern) {
			if ($pattern[0] === '#') {
				// Regex
				if (preg_match($pattern, $ua)) return true;
				continue;
			}
			
			// Substring matches are not abstracted for performance
			$pattern_len = strlen($pattern);
			$ua_len = strlen($ua);

			if ($pattern[0] === '^') {
				// Starts with
				if (strpos($ua, substr($pattern, 1)) === 0) return true;
				
			} else if ($pattern[$pattern_len - 1] === '$') {
				// Ends with
				$pattern_len--;
				$pattern = substr($pattern, 0, $pattern_len);
				if (strpos($ua, $pattern) === ($ua_len - $pattern_len)) return true;
				
			} else {
				// Match anywhere
				if (strpos($ua, $pattern) !== false) return true;
			}
		}
				
		return false;
	}
}

class VirtualCapability_IsRobot extends VirtualCapability {

	protected $required_capabilities = array();

	protected function compute() {
		// Control cap, "controlcap_is_robot" is checked before this function is called
		
		if ($this->wurfl->httpRequest->headerExists("HTTP_ACCEPT_ENCODING") 
			&& $this->wurfl->httpRequest->user_agent->contains("Trident/")
			&& !$this->wurfl->httpRequest->getHeader("HTTP_ACCEPT_ENCODING")->contains("deflate")) {
			return true;
		}
		
		
		// Check against standard bot list
		return $this->wurfl->httpRequest->isRobot();
	}
}

class VirtualCapability_IsFullDesktop extends VirtualCapability {

	protected $required_capabilities = array('ux_full_desktop');

	protected function compute() {
		return $this->wurfl->ux_full_desktop;
	}
}

class VirtualCapability_IsMobile extends VirtualCapability {

	protected $required_capabilities = array('is_wireless_device');

	protected function compute() {
		return $this->wurfl->is_wireless_device;
	}
}

class VirtualCapability_CompleteDeviceName extends VirtualCapability {

	protected $required_capabilities = array(
		'brand_name',
		'model_name',
		'marketing_name',
	);

	protected function compute() {
		$parts = array($this->wurfl->brand_name);
		if (strlen($this->wurfl->model_name)) 
			$parts[] = $this->wurfl->model_name;
		if (strlen($this->wurfl->marketing_name))
			$parts[] = "({$this->wurfl->marketing_name})";

		return implode(' ', $parts);
	}
}

class VirtualCapability_FormFactor extends VirtualCapability {

	protected $required_capabilities = array(
		'ux_full_desktop',
		'is_smarttv',
		'is_wireless_device',
		'is_tablet',
		'can_assign_phone_number',
	);

	public function compute() {
		$map = array(
			'Robot'            => $this->wurfl->is_robot,
			'Desktop'          => $this->wurfl->ux_full_desktop,
			'Smart-TV'         => $this->wurfl->is_smarttv,
			'Other Non-Mobile' => !$this->wurfl->is_wireless_device,
			'Tablet'           => $this->wurfl->is_tablet,
			'Smartphone'       => $this->wurfl->is_smartphone,
			'Feature Phone'    => $this->wurfl->can_assign_phone_number,
		);

		foreach ($map as $type => $condition) {
			if ($condition) {
				return $type;
			}
		}

		return 'Feature Phone';
	}
}


class VirtualCapability_IsSmartphone extends VirtualCapability {
	
	protected $use_caching = true;
	
	protected $required_capabilities = array(
		'is_wireless_device',
		'is_tablet',
		'pointing_method',
		'resolution_width',
		'device_os_version',
		'device_os',
		'can_assign_phone_number',
	);

	protected function compute() {
		if (!$this->wurfl->is_wireless_device) return false;
		if ($this->wurfl->is_tablet) return false;
		if (!$this->wurfl->can_assign_phone_number) return false;
		if ($this->wurfl->pointing_method != 'touchscreen') return false;
		if ($this->wurfl->resolution_width < 320) return false;
		$os_ver = (float)$this->wurfl->device_os_version;
		switch ($this->wurfl->device_os) {
			case 'iOS':
				return ($os_ver >= 3.0);
				break;
			case 'Android':
				return ($os_ver >= 2.2);
				break;
			case 'Windows Phone OS':
				return true;
				break;
			case 'RIM OS':
				return ($os_ver >= 7.0);
				break;
			case 'webOS':
				return true;
				break;
			case 'MeeGo':
				return true;
				break;
			case 'Bada OS':
				return ($os_ver >= 2.0);
				break;
			default:
				return false;
				break;
		}
	}
}

class VirtualCapability_ManualGroupChild extends VirtualCapability {
	protected $use_caching = false;
	protected $manual_value;
	/**
	 * @var VirtualCapabilityGroup
	 */
	protected $group;
	
	public function __construct(TeraWurfl $wurfl, VirtualCapabilityGroup $group, $value=null) {
		$this->group = $group;
		parent::__construct($wurfl);
		$this->manual_value = $value;
	}
	
	public function compute() {
		return $this->manual_value;
	}
	
	public function hasRequiredCapabilities() {
		return $this->group->hasRequiredCapabilities();
	}
	
	public function getRequiredCapabilities() {
		return $this->group->getRequiredCapabilities();
	}
}

abstract class VirtualCapabilityGroup {
	
	protected $required_capabilities = array();
	protected $virtual_capabilities = array();
	protected $storage = array();
	
	private static $loaded_capabilities;
	
	/**
	 * @var TeraWurfl
	 */
	protected $wurfl;
	
	public function __construct(TeraWurfl $wurfl) {
		$this->wurfl = $wurfl;
	}
	
	public function hasRequiredCapabilities() {
		if (empty($this->required_capabilities)) return true;
		if (self::$loaded_capabilities === null) {
			self::$loaded_capabilities = $this->wurfl->getLoadedCapabilityNames();
		}
		$missing_caps = array_diff($this->required_capabilities, self::$loaded_capabilities);
		return empty($missing_caps);
	}
	
	public function getRequiredCapabilities() {
		return $this->required_capabilities;
	}
	
	abstract public function compute();
	
	public function get($name) {
		return $this->storage[$name];
	}
}

class VirtualCapabilityGroup_DeviceBrowser extends VirtualCapabilityGroup {
	
	protected $required_capabilities = array();
	
	protected $storage = array(
		'DeviceOs' => null,
		'DeviceOsVersion' => null,
		'Browser' => null,
		'BrowserVersion' => null,
	);
	
	/**
	 * @var VirtualCapability_UserAgentTool
	 */
	protected static $ua_tool;
	
	public function compute() {
		if (self::$ua_tool === null) {
			if (!class_exists('VirtualCapability_UserAgentTool', false)) {
				include dirname(__FILE__).'/VirtualCapability_UserAgentTool.php';
			}
			self::$ua_tool = new VirtualCapability_UserAgentTool();
		}
		
		// Run the UserAgentTool to get the relevant details
		$device = self::$ua_tool->getDevice($this->wurfl->httpRequest);
		
		$this->storage['DeviceOs'] = new VirtualCapability_ManualGroupChild($this->wurfl, $this, $device->os->name);
		$this->storage['DeviceOsVersion'] = new VirtualCapability_ManualGroupChild($this->wurfl, $this, $device->os->version);
		$this->storage['Browser'] = new VirtualCapability_ManualGroupChild($this->wurfl, $this, $device->browser->name);
		$this->storage['BrowserVersion'] = new VirtualCapability_ManualGroupChild($this->wurfl, $this, $device->browser->version);
	}
}
