<?php if ( ! defined('BASEPATH')) exit('Invalid file request');

/**
 * Pickr model.
 *
 * @package			Pickr
 * @author 			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright 		Experience Internet
 * @version 		0.1.0
 */

require_once PATH_THIRD .'pickr/classes/pickr_exceptions' .EXT;

class Pickr_model extends CI_Model {
	
	/* --------------------------------------------------------------
	 * PRIVATE PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * The API connector.
	 *
	 * @access	private
	 * @var		Pickr_flickr
	 */
	private $_api_connector;
	
	/**
	 * The API credentials.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_api_credentials;
	
	/**
	 * The ExpressionEngine singleton.
	 *
	 * @access	private
	 * @var		object
	 */
	private $_ee;
	
	/**
	 * Extension class. Assumed to be the package name,
	 * with an `_ext` suffix.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_extension_class;
	
	/**
	 * Flickr buddy icon member field ID.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_flickr_buddy_icon_member_field_id;
	
	/**
	 * Flickr username member field ID.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_flickr_username_member_field_id;
	
	/**
	 * Package name.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_package_name;
	
	/**
	 * Package version.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_package_version;
	
	/**
	 * The site ID.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_site_id;
	
	
	
	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct()
	{
		parent::CI_Model();
		
		$this->_ee =& get_instance();
		$this->_ee->load->helper('pickr_number_helper');
		
		$this->_flickr_buddy_icon_member_field_id	= 'm_field_id_20';
		$this->_flickr_username_member_field_id 	= 'm_field_id_10';
		
		$this->_package_name	= 'Pickr';
		$this->_package_version	= '0.1.0';
		$this->_site_id 		= $this->_ee->config->item('site_id');
		$this->_extension_class = ucfirst($this->get_package_name() .'_ext');
		
		// API credentials are hard-coded at present.
		$this->_api_credentials	= array(
			'api_key'		=> 'ea038ecba7beb53f558fbb7bfea394bd',
			'secret_key'	=> '0a5a45d2ba2d0121'
		);
	}
	
	
	/**
	 * Activates the extension.
	 *
	 * @access	public
	 * @return	void
	 */
	public function activate_extension()
	{
		$data = array(
			'class'		=> $this->get_extension_class(),
			'enabled'	=> 'y',
			'hook'		=> 'member_register_validate_members',
			'method'	=> 'on_member_register_validate_members',
			'settings'	=> '',
			'version'	=> $this->get_package_version()
		);
		
		$this->_ee->db->insert('extensions', $data);
	}
	
	
	/**
	 * Disables the extension.
	 *
	 * @access	public
	 * @return	void
	 */
	public function disable_extension()
	{
		$this->_ee->db->delete('extensions', array('class' => $this->get_extension_class()));
	}
	
	
	/**
	 * Returns the API credentials. Hard-coded at present, but may be
	 * loading from settings in the future.
	 *
	 * @access	public
	 * @return	array
	 */
	public function get_api_credentials()
	{
		return $this->_api_credentials;
	}
	
	
	/**
	 * Returns the extension class name.
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_extension_class()
	{
		return $this->_extension_class;
	}
	
	
	/**
	 * Returns the Flickr buddy icon member field ID. Hard-coded at present,
	 * but could be moved to a config file or settings screen in the future.
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_flickr_buddy_icon_member_field_id()
	{
		return $this->_flickr_buddy_icon_member_field_id;
	}
	
	
	/**
	 * Returns a Flickr user's NSID, given the username.
	 *
	 * @access	public
	 * @param	string	$username	The Flickr username.
	 * @return	array
	 */
	public function get_flickr_nsid_from_username($username)
	{
		$this->_validate_api_connector();
		
		$result = $this->_api_connector->people_find_by_username($username);
		return $result['user']['nsid'];
	}
	
	
	/**
	 * Returns the Flickr user's buddy icon URL.
	 *
	 * @access	public
	 * @param	string	$nsid	The Flickr user's NSID.
	 * @return	array
	 */
	public function get_flickr_user_buddy_icon($nsid)
	{
		$this->_validate_api_connector();
		
		$result = $this->_api_connector->people_get_info($nsid);
		
		$icon_farm		= $result['person']['iconfarm'];
		$icon_server	= $result['person']['iconserver'];
		
		return 'http://farm' .$icon_farm .'.static.flickr.com/' .$icon_server .'/buddyicons/' .$nsid .'.jpg';
	}
	
	
	/**
	 * Returns the Flickr username member field ID. Hard-coded at present,
	 * but could be moved to a config file or settings screen in the future.
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_flickr_username_member_field_id()
	{
		return $this->_flickr_username_member_field_id;
	}
	
	
	/**
	 * Loads a member's Flickr buddy icon. A convenience wrapper for a bunch of other
	 * methods.
	 *
	 * @access	public
	 * @param	int|string		$member_id		The member ID.
	 * @return	bool
	 */
	public function get_member_flickr_buddy_icon($member_id)
	{
		if ( ! $username = $this->get_member_flickr_username($member_id))
		{
			/**
			 * Not entirely comfortable with this, but then it doesn't
			 * seem like an exceptional circumstance either.
			 */
			
			return FALSE;
		}
		
		/**
		 * Retrieve the buddy icon. If anything goes wrong, an exception is
		 * throw, and we just let it bubble.
		 */
		
		$user_id	= $this->get_flickr_nsid_from_username($username);
		$icon_url	= $this->get_flickr_user_buddy_icon($user_id);
		
		// Save the icon to the database.
		$this->save_member_flickr_buddy_icon($icon_url);
		
		return TRUE;
	}
	
	
	/**
	 * Loads a member's Flickr username, given the member ID.
	 *
	 * @access	public
	 * @param	int|string		$member_id		The member ID.
	 * @return	string
	 */
	public function get_member_flickr_username($member_id)
	{
		$username = '';
		
		// Get out early. Uses pickr_number_helper function.
		if ( ! valid_database_id($member_id))
		{
			return $username;
		}
		
		// Shortcuts.
		$db =& $this->_ee->db;
		
		$db_member = $db->select($this->get_flickr_username_member_field_id())
						->get_where('member_data', array('member_id' => $member_id));
		
		if ($db_member->num_rows() === 1)
		{
			$username = $db_member->row()->m_field_id_10;
		}
		
		return $username;
	}
	
	
	/**
	 * Returns the package name.
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_package_name()
	{
		return $this->_package_name;
	}
	
	
	/**
	 * Returns the package version.
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_package_version()
	{
		return $this->_package_version;
	}
	
	
	/**
	 * Returns the site ID.
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_site_id()
	{
		return $this->_site_id;
	}
	
	
	/**
	 * Saves a Flickr buddy icon URL to the database.
	 *
	 * @access	public
	 * @param	int|string		$member_id		The member ID.
	 * @param 	string 			$url 			The buddy icon URL.
	 * @return	bool
	 */
	public function save_member_flickr_buddy_icon($member_id, $url)
	{
		// Get out early. Uses pickr_number_helper function.
		if ( ! valid_database_id($member_id))
		{
			return FALSE;
		}
		
		$member_field = $this->get_flickr_buddy_icon_member_field_id();
		
		$this->_ee->db->update(
			'member_data',
			array($member_field	=> $url),
			array('member_id'	=> $member_id)
		);
		
		return (bool) $this->_ee->db->affected_rows();
	}
	
	
	/**
	 * Sets the API connector.
	 *
	 * @access	public
	 * @param	Pickr_flickr	$connector		The API connector.
	 * @return	void
	 */
	public function set_api_connector(Pickr_flickr $connector)
	{
		$this->_api_connector = $connector;
	}
	
	
	/**
	 * Updates the extension.
	 *
	 * @access	public
	 * @param	string		$current_version		The currently installed version.
	 * @param	string		$update_version			The version we're upgrading to.
	 * @return	bool|void
	 */
	public function update_extension($current_version = '', $update_version = '')
	{
		if ($current_version == ''
			OR version_compare($current_version, $update_version, '>='))
		{
			return FALSE;
		}
		
		$this->_ee->db->update(
			'extensions',
			array('version' => $update_version),
			array('class' => $this->get_extension_class())
		);
	}
	
	
	
	/* --------------------------------------------------------------
	 * PRIVATE METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Checks whether the API connector has been set. If not, throws an
	 * exception.
	 *
	 * @access	private
	 * @return	void
	 */
	private function _validate_api_connector()
	{
		if ( ! $this->_api_connector instanceof Pickr_flickr)
		{
			throw new Pickr_exception('API connector not set.');
		}
	}
	
}

/* End of file 		: pickr_model.php */
/* File location	: third_party/pickr/models/pickr_model.php */