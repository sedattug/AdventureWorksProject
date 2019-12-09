<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class MY_Lang extends CI_Lang {
 
        /**
	 * Fetch a single line of text from the language array
	 *
	 * @access	public
	 * @param	string	$line	the language line
	 * @return	string
	 */
	function line($line = '', $log_errors = true)
	{
		$value = ($line == '' OR ! isset($this->language[$line])) ? FALSE : $this->language[$line];

		// Because killer robots like unicorns!
		if ($value === FALSE)
		{
			//log_message('error', 'Could not find the language line "'.$line.'"');
			log_message('info', 'Could not find the language line "'.$line.'"');
		}

		return $value;
	}
 
}