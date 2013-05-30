<?php defined('SYSPATH') OR die('No direct script access.');

class Arr extends Kohana_Arr {

	/**
	 * Unset all keys that have empty string values
	 *
	 * @static
	 * @param array $input
	 * @param boolean $recursive
	 * @return array
	 */
	public static function unset_empty(array &$input, $recursive=false)
	{
		foreach ($input as $key => $value) {
			if($recursive == true && is_array($value)) {
				$input[$key] = self::unset_empty($input[$key]);
			}
			else if (is_string($value) && $value == '') {
				unset($input[$key]);
			}
		}

		return $input;
	}

	/**
	 * Re-order an array based on values
	 *
	 * @param array $array Array source where we'll be moving the value
	 * @param string $value The value you'd like to move
	 * @param string $position (end|start|before|after)
	 * @param string $relative Which value to position this value with (only when $position[before|after])
	 */
	public static function place_value(array $array, $value, $position='end', $relative=null) {
		//remove the existing value
		if(in_array($value, $array))
			unset($array[array_search($value,$array)]);

		//look for the insert pointer
		if($position == 'start')
		{
			$insertion_point = 0;
		}
		else if($position == 'end')
		{
			$insertion_point = count($array);
		}
		else if(in_array($position, array('before', 'after')))
		{
			$insertion_point = array_search($relative, $array);

			if($position == 'after')
				$insertion_point++;
		}
		else
			return false;

		$before = array_slice($array, 0, $insertion_point, true);
		$after  = array_slice($array, $insertion_point, null, true);

		$array = array_merge($before, array($value), $after);
		return $array;

	}

	/**
	 * Re-order an array based on keys
	 *
	 * @param array $array Array source where we'll be moving the value
	 * @param string $key The key you'd like to move
	 * @param mixed $value The value of the key you're adding
	 * @param string $position (end|start|before|after)
	 * @param string $relative Which key to position this key with (only needed when $position[before|after])
	 * @param boolean $move The provided $value is omitted
	 */
	protected static function _place_key(array &$array, $key, $value=null, $position='end', $relative=null, $move=false) {
		//Check for an existing key
		if(array_key_exists($key, $array))
		{
			//overwrite the provided $value, we're just moving the key
			if($move)
				$value = $array[$key];

			//remove the existing key-value pair
			unset($array[$key]);
		}

		switch($position) {
			case 'start':
				$insertion_point = 0;
				break;
			case 'before':
				$insertion_point = array_search($relative, array_keys($array));
				break;
			case 'after':
				$insertion_point = array_search($relative, array_keys($array))+1;
				break;
			default:
				$insertion_point = count($array);
				break;
		}

		$before = array_slice($array, 0, $insertion_point, true);
		$after  = array_slice($array, $insertion_point, null, true);

		$array = array_merge($before, array($key => $value), $after);

		return true;
	}

	/**
	 * @see Arr::_place_key()
	 * @return bool
	 */
	public static function add(array &$array, $key, $value=null, $position='end', $relative=null, $move=false) {
		return self::_place_key($array, $key, $value, $position, $relative, $move);
	}

	/**
	 * @see Arr::_place_key()
	 * @return bool
	 */
	public static function before(array &$array, $key, $value=null, $relative=null, $move=false) {
		return self::_place_key($array, $key, $value, 'before', $relative, $move);
	}

	/**
	 * @see Arr::_place_key()
	 * @return bool
	 */
	public static function after(array &$array, $key, $value=null, $relative=null, $move=false) {
		return self::_place_key($array, $key, $value, 'after', $relative, $move);
	}


	/**
	 * @see Arr::_place_key()
	 * @return bool
	 */
	public static function move(array &$array, $key, $position='end', $relative=null) {
		return self::_place_key($array, $key, null, $position, $relative, true);
	}

	/**
	 * Returns a list containing all paths this array holds
	 *
	 * @return array
	 */
	public static function paths(array $array) {
		$output = array();

		foreach($array as $key => $value) {
			if(is_array($value))
				self::_flatten($value, $key, $output);
			else
				$output[$key] = $value;
		}

		return $output;
	}

	protected static function _flatten(&$array, $index, &$output=array()) {
		foreach($array as $key => $value) {
			if(is_array($value))
				self::_flatten($value, $index.'.'.$key, $output);
			else
				$output[$index.'.'.$key] = $value;
		}
		return $output;
	}


	/**
	 * Remove an array entry by path
	 *
	 * @param $array array
	 * @param $path array|string
	 * @param null $delimiter
	 * @return bool
	 */
	public static function remove(&$array, $path, $delimiter = NULL)
	{
		if ( ! Arr::is_array($array))
		{
			// This is not an array!
			return false;
		}

		if (is_array($path))
		{
			// The path has already been separated into keys
			$keys = $path;
		}
		else
		{
			if (array_key_exists($path, $array))
			{
				// No need to do extra processing
				unset($array[$path]);
				return true;
			}

			if ($delimiter === NULL)
			{
				// Use the default delimiter
				$delimiter = Arr::$delimiter;
			}

			// Remove starting delimiters and spaces
			$path = ltrim($path, "{$delimiter} ");

			// Remove ending delimiters, spaces, and wildcards
			$path = rtrim($path, "{$delimiter} *");

			// Split the keys by delimiter
			$keys = explode($delimiter, $path);
		}

		do
		{
			$key = array_shift($keys);

			if (ctype_digit($key))
			{
				// Make the key an integer
				$key = (int) $key;
			}

			if (isset($array[$key]))
			{
				if ($keys)
				{
					if (Arr::is_array($array[$key]))
					{
						// Dig down into the next part of the path
						$array = $array[$key];
					}
					else
					{
						// Unable to dig deeper
						break;
					}
				}
				else
				{
					// Found the path requested
					unset($array[$key]);
					return true;
				}
			}
			elseif ($key === '*')
			{
				// Handle wildcards

				$success = false;

				foreach ($array as $arr)
				{
					if (Arr::remove($arr, implode('.', $keys)))
					{
						$success = true;
					}
				}

				return $success;
			}
			else
			{
				// Unable to dig deeper
				break;
			}
		}
		while ($keys);

		// Unable to find the value requested
		return false;
	}
}