<?php defined('SYSPATH') OR die('No direct script access.');


class Config_Group extends Kohana_Config_Group {

	public function export($location) {
		$array = serialize($this->getArrayCopy());

		$export = str_replace(array('  ', 'array (', "'true'", "'false'",), array("\t", "\tarray(", 'true', 'false'), var_export($array, true));
		$export = stripslashes($export);

		$content = "<?php defined('SYSPATH') OR die('No direct script access.');" . PHP_EOL . PHP_EOL;
		$content .= 'return ' . $export . ';';

		if(file_exists($location) === false)
			mkdir($location);

		return (file_put_contents($location.$this->_group_name.'.php', $content));
	}

	/**
	 * Returns a list containing all paths this config file holds
	 *
	 * @return array
	 */
	public function flatten() {
		return Arr::paths($this->getArrayCopy());
	}
}
