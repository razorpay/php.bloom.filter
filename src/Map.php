<?php

namespace Razorpay\BloomFilter;

use Exception;

/**
* MapClass
* Use cases:
* -check user's setup parameters and merge it with default one
*
* Map view:
* paramskey => arrayMap
*
* ArrayMap view
* -null (boolean): checks required parameter
* -type (string): checks for type of parameter (values from gettype())
* -min, max (float, int): allowed range of number value
*
* Throws Ecpetion with message
*/
class Map {
	
	/**
	* Main method, applie's default and given parameters with map
	*
	* @param array map
	* @param array default parameters
	* @param array given parameters
	* @return array merged result parameters
	*/
	static public function apply($map, $initial, $setup) {
		self::circl($map, $setup);	
		return array_merge($initial, $setup);
	}
	
	/**
	* Recursively follows map
	*
	* @param array map
	* @param array given parameters
	*/
	static private function circl($map, $rabbit) {
		foreach($map as $k => $element) {
			if (is_array($element) && (!array_key_exists('type', $element) || !$element['type']) && array_key_exists($k, $rabbit)) {
				unset($rabbit[$k]);
				self::circl($element, $rabbit[$k]);
			} else if (array_key_exists($k, $rabbit)) {
				self::check($element, $rabbit[$k]);
				unset($rabbit[$k]);
			}
		}
		
		if($rabbit)
			throw new Exception('Unexpected array arguments. '.json_encode( $rabbit ));
	}
	
	/**
	* Check map rules for given object
	*
	* @param array map
	* @param mixed given parameters element
	*/
	static private function check($map, $rabbit) {
		/**
		*	required statement check
		*/
		if (array_key_exists('null', $map) && $map['null'] === false && !$rabbit)
			throw new Exception('Must be not NULL');
		
		/**
		*	If no element exists, exit
		*/
		if(!$rabbit)
			return true;
			
		/**
		*	Check for type
		*/
		if (array_key_exists('type', $map) && $map['type'] !== gettype($rabbit) && $map['type'])
			throw new Exception('Wrong type '.gettype($rabbit).'! Must be '.$map['type']);
		
		/**
		*	Check for minimal range
		*/
		if (array_key_exists('min', $map) && $map['min'] > $rabbit && $map['min'] !== null)
			throw new Exception('Interval overflow by '.$rabbit.'! Must be '.$map['min']);
			
		/**
		*	Check for maximal range
		*/
		if (array_key_exists('min', $map) && $map['min'] > $rabbit && $map['min'] !== null)
			throw new Exception('Interval overflow by '.$rabbit.'! Must be '.$map['max']);	
	}
}