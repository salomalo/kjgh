<?php
/**
* @package      PayPlans
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

/**
 * JRegistry wrapper
 *
 * @since	4.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class PPRegistry extends PayPlans
{
	/**
	 * Helper object.
	 * @var	object
	 */
	private $helper 	= null;

	/**
	 * Class constructor
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function __construct($data = '')
	{
		// Load our helpers
		$name = PP::getJoomlaCodename();
		$path = dirname(__FILE__) . '/helpers/' . $name . '.php';

		require_once($path);

		$className = 'PPRegistry' . ucfirst($name);

		$this->helper = new $className('');

		// Always use our own load methods.
		if (!empty($data)) {
			$this->load($data);
		}

		return $this;
	}

	/**
	 * Creates a copy of it self and return to the caller.
	 *
	 * @since	4.0
	 * @access	public
	 *
	 */
	public static function factory($data = '')
	{
		return new self($data);
	}


	/**
	 * Maps back the call method functions to the helper.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function __call($method, $args)
	{
		$refArray = array();

		if ($args) {
			foreach ($args as &$arg) {
				$refArray[] =& $arg;
			}
		}
		return call_user_func_array(array($this->helper, $method), $refArray);
	}

	/**
	 * Gets the real registry helper.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getRegistry()
	{
		return $this->helper;
	}

	public function render($name = 'params', $group = '_default')
	{
		$params = $this->helper->getParams($name, $group);

		return PP::get('Themes')->set('params', $params)->output('admin.parameters.default');
	}

	/**
	 * Merge a JRegistry object into this one
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function mergeObjects($extended, $intelligentMerging = false, $debug = false)
	{
		if (!$extended instanceof JRegistry && !$extended instanceof PPRegistry) {
			return false;
		}

		// Load the variables into the registry's default namespace.
		$extendedData = $extended->toArray();
		$localData = $this->helper->toArray();

		if ($debug) {
			// dump($localData, $extendedData);
		}

		$result = $this->mergeArrays($localData, $extendedData, $intelligentMerging);
		$this->helper->setData($result);

		return true;
	}

	/**
	 * Merge arrays
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function mergeArrays($source, $extended, $intelligentMerging = false)
	{
		// Loop through all the extended keys
		foreach ($extended as $key => $value) {
			// If the key exists and it's an array, we need to run this recursively.
			if (is_array($source) && array_key_exists($key, $source) && is_array($value)) {
				$source[ $key ]	= $this->mergeArrays($source[ $key ], $extended[ $key ], $intelligentMerging);
			} else {
				// If this is intelligent merging, true always wins
				if ($intelligentMerging) {
					// If key does not exist, then we merge it.
					if (!isset($source[ $key ])) {
						$source[ $key ]	= $extended[ $key ];
					} else {
						$source[ $key ]	= $extended[ $key ] > $source[ $key ] ? $extended[ $key ] : $source[ $key ];
					}
				} else {
					$source[ $key ]	= $extended[ $key ];
				}
			}
		}

		return $source;
	}

	/**
	 * Override bind's behavior by allowing passing in as string for data.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function bind($data)
	{
		if (is_string($data)) {
			$json = PP::json();
			$data = $json->decode($data);
		}

		return call_user_func_array(array($this->helper, __FUNCTION__), array($data));
	}

	/**
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function save()
	{
		echo '<pre>';
		var_dump($this);
		echo '</pre>';
		exit;
	}

	/**
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function load($data)
	{
		$obj = PP::makeObject($data);

		if ($obj) {
			foreach ($obj as $key => $value) {
				$this->helper->set($key, $value);
			}
		}

		return true;
	}

}
