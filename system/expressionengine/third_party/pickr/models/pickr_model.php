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
	 * The ExpressionEngine singleton.
	 *
	 * @access	private
	 * @var		object
	 */
	private $_ee;
	
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
		$this->_ee =& get_instance();
		$this->_ee->load->helper('pickr_number_helper');
		
		$this->_flickr_buddy_icon_member_field_id	= 'm_field_id_20';
		$this->_flickr_username_member_field_id 	= 'm_field_id_10';
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
	
}

/* End of file 		: pickr_model.php */
/* File location	: third_party/pickr/models/pickr_model.php */