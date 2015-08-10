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
require_once dirname(__FILE__).'/VirtualCapability.php';

/**
 * Provides access to virtual capabilities
 * @package TeraWurflVirtualCapability
 */
class VirtualCapabilityProvider {
	
	const PREFIX_VIRTUAL = '';
	const PREFIX_CONTROL = 'controlcap_';
	const WURFL_CONTROL_GROUP = 'virtual';
	const WURFL_CONTROL_DEFAULT = 'default';
	
	/**
	 * @var TeraWurfl
	 */
	protected $wurfl;
	
	public function __construct(TeraWurfl $wurfl) {
		$this->wurfl = $wurfl;
	}
	
	/**
	 * Map of WURFL names to VirtualCapability classes.
	 * @var array
	 */
	public static $virtual_capabilities = array(
		'is_android' => 'IsAndroid',
		'is_ios' => 'IsIos',
		'is_windows_phone' => 'IsWindowsPhone',
		'is_app' => 'IsApp',
		'is_full_desktop' => 'IsFullDesktop',
		'is_largescreen' => 'IsLargescreen',
		'is_mobile' => 'IsMobile',
		'is_robot' => 'IsRobot',
		'is_smartphone' => 'IsSmartphone',
		'is_touchscreen' => 'IsTouchscreen',
		'is_wml_preferred' => 'IsWmlPreferred',
		'is_xhtmlmp_preferred' => 'IsXhtmlmpPreferred',
		'is_html_preferred' => 'IsHtmlPreferred',
		'advertised_device_os' => 'DeviceBrowser.DeviceOs',
		'advertised_device_os_version' => 'DeviceBrowser.DeviceOsVersion',
		'advertised_browser' => 'DeviceBrowser.Browser',
		'advertised_browser_version' => 'DeviceBrowser.BrowserVersion',
		'complete_device_name' => 'CompleteDeviceName',
		'form_factor' => 'FormFactor',
		'is_phone' => 'IsPhone',
		'is_app_webview' => 'IsAppWebview',
	);
	
	/**
	 * Storage for the VirtualCapability objects
	 * @var array
	 */
	protected $cache = array();
	
	/**
	 * Storage for the VirtualCapabilityCache objects
	 * @var array
	 */
	protected $group_cache = array();
	
	/**
	 * Returns the names of all the available virtual capabilities
	 * @return array
	 */
	public function getNames() {
		return array_keys(self::$virtual_capabilities);
	}
	
	/**
	 * Returns an array of all the required capabilities for all virtual capabilities
	 * @return array
	 */
	public static function getRequiredCapabilities() {
		$caps = array();
		
		$model_wurfl = new TeraWurfl();
		
		foreach (self::$virtual_capabilities as $cap_name => $vc_name) {
			if (strpos($vc_name, '.') !== false) {
				// Group of capabilities
				list($group, $property) = explode('.', $vc_name);
				$class = "VirtualCapabilityGroup_$group";
			} else {
				// Individual capability
				$class = "VirtualCapability_$vc_name";
			}
			
			$model = new $class($model_wurfl);
			$caps = array_unique(array_merge($caps, $model->getRequiredCapabilities()));
			unset($model);
		}
		
		return $caps;
	}
	
	/**
	 * Gets an array of all the virtual capabilities
	 * @return array Virtual capabilities in format "name => value"
	 */
	public function getAll() {
		$all = array();
		foreach (self::$virtual_capabilities as $name => $class) {
			$all[self::PREFIX_VIRTUAL.$name] = $this->get($name);
		}
		return $all;
	}
	
	/**
	 * Returns the value of the virtual capability
	 * @param string $name
	 * @return string|bool|int|float
	 */
	public function get($name) {
		$control_value = $this->getControlValue($name);
		
		// The value is null if it is not in the loaded WURFL, it's default if it is loaded and not overridden
		if ($control_value === null || $control_value == self::WURFL_CONTROL_DEFAULT) {
			// The control capability was not used, use the VirtualCapability provider
			return $this->getObject($name)->getValue();
		}
		
		// Forced capabilities
		if ($control_value === 'force_true') return true;
		if ($control_value === 'force_false') return false;
		
		// Use the control value from WURFL
		return $control_value;
	}
	
	/**
	 * Returns the VirtualCapability object for the given $name.
	 * @param string $name
	 * @return VirtualCapability
	 */
	public function getObject($name) {
		$name = $this->cleanCapabilityName($name);
		if (!array_key_exists($name, $this->cache)) {
			if (($pos = strpos(self::$virtual_capabilities[$name], '.')) !== false) {
				// Group of capabilities
				list($group, $property) = explode('.', self::$virtual_capabilities[$name]);
				
				if (!array_key_exists($group, $this->group_cache)) {
					$class = 'VirtualCapabilityGroup_'.$group;
					// Cache the group
					$this->group_cache[$group] = new $class($this->wurfl);
					$this->group_cache[$group]->compute();
				}
				
				// Cache the capability
				$this->cache[$name] = $this->group_cache[$group]->get($property);
			} else {
				// Individual capability
				$class = 'VirtualCapability_'.self::$virtual_capabilities[$name];
				$this->cache[$name] = new $class($this->wurfl);
			}
		}
		return $this->cache[$name];
	}
	
	/**
	 * True if the virtual capability exists
	 * @param string $name
	 * @return boolean
	 */
	public function exists($name) {
		return array_key_exists($this->cleanCapabilityName($name), self::$virtual_capabilities);
	}
	
	protected function getControlValue($name) {
		// Check if loaded WURFL contains control caps
		if (!array_key_exists(self::WURFL_CONTROL_GROUP, $this->wurfl->capabilities)) {
			return null;
		}
		$control_cap = self::PREFIX_CONTROL.$this->cleanCapabilityName($name);
		// Check if loaded WURFL contains the requested control cap
		if (!array_key_exists($control_cap, $this->wurfl->capabilities[self::WURFL_CONTROL_GROUP])) {
			return null;
		}
		return $this->wurfl->capabilities[self::WURFL_CONTROL_GROUP][$control_cap];
	}
	
	protected function cleanCapabilityName($name) {
		return str_replace(self::PREFIX_VIRTUAL, '', $name);
	}
}