<?php defined('SYSPATH') OR die('No direct script access.');

class Config_Group extends Kohana_Config_Group {

	public function export($location) {
		if(file_exists($location) === false)
			mkdir($location);
		
		if(substr($location, -1) != DIRECTORY_SEPARATOR)
		{
			$location .= DIRECTORY_SEPARATOR;
		}

		$file = $location.$this->_group_name.EXT;

		// Write in file
		if ( ! $h = fopen($file, 'w+'))
		{
			return FALSE;
		}

		// Block access to the file
		if (flock($h, LOCK_EX))
		{
			$array = $this->getArrayCopy();

			// Modifiers for adjusting appearance
			$replace = array(
				"=> \n"    => '=>',
				'array ('  => 'array(',
				'  '       => "\t",
				' false,'  => ' FALSE,',
				' true,'   => ' TRUE,',
				' null,'   => ' NULL,',
				MODPATH    => 'MODPATH',
				APPPATH    => 'APPPATH',
				SYSPATH    => 'SYSPATH',
				DOCROOT    => 'DOCROOT'
			);

			$array = var_export($array, true);
			$var = stripslashes(strtr($array, $replace));

			$content = Kohana::FILE_SECURITY.PHP_EOL.PHP_EOL.'return '.$var.';';

			$result = fwrite($h, $content);
			flock($h, LOCK_UN);
		}
		fclose($h);

		return (bool) $result;
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
