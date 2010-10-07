<?php if ( ! defined('BASEPATH')) exit('Invalid file request');

/**
 * Connects to the Flickr API.
 *
 * @see				http://www.flickr.com/services/api/
 * @package			Pickr
 * @author 			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright 		Experience Internet
 * @version 		0.1.0
 */

class Pickr_flickr {
	
	/* --------------------------------------------------------------
	 * PRIVATE PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * The API endpoint URL.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_api_url;
	
	/**
	 * API key.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_api_key;
	
	/**
	 * Secret key.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_secret_key;
	
	
	
	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @param 	string 		$api_key		The API key.
	 * @param	string		$secret_key		The secret key.
	 * @return	void
	 */
	public function __construct($api_key = '', $secret_key = '')
	{
		$this->_api_url		= 'http://api.flickr.com/services/rest/';
		$this->_api_key		= $api_key;
		$this->_secret_key	= $secret_key;
	}
	
	
	/**
	 * Returns data about a Flickr user, given his username.
	 *
	 * @see 	http://www.flickr.com/services/api/flickr.people.findByUsername.html
	 * @access	public
	 * @param	string		$username		The Flickr username.
	 * @return	array
	 */
	public function people_find_by_username($username)
	{
		return $this->_call_api('flickr.people.findByUsername', array('username' => $username));
	}
	
	
	/**
	 * Returns data about a Flickr user, given his NSID.
	 *
	 * @see		http://www.flickr.com/services/api/flickr.people.getInfo.htm
	 * @access	public
	 * @param	string		$nsid			The Flickr NSID.
	 * @return	array
	 */
	public function people_get_info($nsid)
	{
		return $this->_call_api('flickr.people.getInfo', array('user_id' => $nsid));
	}
	
	
	/* --------------------------------------------------------------
	 * PRIVATE METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Makes the call to the Flickr API, and handles the response.
	 *
	 * @access	private
	 * @param	string		$method		The REST method.
	 * @param	array		$args		The method arguments.
	 * @return	array
	 */
	private function _call_api($method, Array $args = array())
	{
		// Do we have the required credentials.
		if ( ! $this->_api_key OR ! $this->_secret_key)
		{
			throw new Pickr_exception('API credentials not set.');
		}
		
		// Do we have a method to call?
		if ( ! $method OR ! is_string($method))
		{
			throw new Pickr_exception('API method not specified.');
		}
		
		// Add all the required arguments to the args array.
		$args = array_merge($args, array(
			'api_key'	=> $this->_api_key,
			'format'	=> 'php_serial',
			'method'	=> $method
		));
		
		// Prepare the arguments.
		$clean_args = array();
		
		foreach ($args AS $key => $val)
		{
			$clean_args[] = urlencode($key) .'=' .urlencode($val);
		}
		
		// Construct the URL.
		$api_url = $this->_api_url .'?' .implode('&', $clean_args);
		
		// Make the call.
		$result = unserialize(file_get_contents($api_url));
		
		// Did we crash and burn?
		if ($result['stat'] != 'ok')
		{
			throw new Pickr_api_exception($result['message'], intval($result['code']));
		}
		
		return $result;
	}
	
}

/* End of file 		: pickr_flickr.php */
/* File location	: third_party/pickr/libraries/pickr_flickr.php */