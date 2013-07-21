<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Session extends Kohana_Session {

	/**
	 * Use Arr::path() to retrieve a value stored in sessions.
	 *
	 * @param   mixed   $path       key path string (delimiter separated) or array of keys
	 * @param   mixed   $default    default value if the path is not set
	 * @param   string  $delimiter  key path delimiter
	 * @return  mixed
	 */
	public function path($path, $default = NULL, $delimiter = NULL) {
		return Arr::path($this->_data, $path, $default, $delimiter);
	}
}
