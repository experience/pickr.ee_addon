<?php if ( ! defined('BASEPATH')) exit('Invalid file request');

/**
 * Pickr model.
 *
 * @package			Pickr
 * @author 			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright 		Experience Internet
 * @version 		0.1.0
 */

class Pickr_model extends CI_Model {
	
	/* --------------------------------------------------------------
	 * PRIVATE PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * The ExpressionEngine singleton.
	 *
	 * @access	private
	 * @var		object
	 */
	private $_ee;
	
	
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
		$this->_flickr_username_member_field_id = 'm_field_id_10';
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
		// Shortcuts.
		$db =& $this->_ee->db;
		
		$db_member = $db->select($this->get_flickr_username_member_field_id())
						->get_where('member_data', array('member_id' => $member_id));
		
		return $db_member->num_rows() === 1
			? $db_member->row()->m_field_id_10
			: '';
	}
	
	
	/**
	 * Saves a Flickr photo URL to the database.
	 *
	 * @access	public
	 * @param	int|string		$member_id		The member ID.
	 * @param 	string 			$url 			The photo URL.
	 * @return	bool
	 */
	public function save_member_flickr_photo($member_id, $url)
	{
		$this->_ee->db->update(
			'member_data',
			array('m_field_id_20' => $url),
			array('member_id' => $member_id)
		);
		
		return (bool) $this->_ee->db->affected_rows();
	}
	
}

/* End of file 		: pickr_model.php */
/* File location	: third_party/pickr/models/pickr_model.php */