<?php if ( ! defined('BASEPATH')) exit('Invalid file request');

/**
 * Pickr number helper methods.
 *
 * @package			Pickr
 * @author 			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright 		Experience Internet
 * @version 		0.1.0
 */

if ( ! function_exists('valid_database_id'))
{
	/**
	 * Returns whether the supplied argument is a number or string
	 * that may be interpreted as a positive integer.
	 *
	 * @access	private
	 * @param	mixed		$val		The value to test.
	 * @return	bool
	 */
	function valid_database_id($val)
	{
		return ($val && is_numeric($val) && intval($val) == $val);
	}
}

/* End of file 		: pickr_number_helper.php */
/* File location	: third_party/pickr/helpers/pickr_number_helper.php */