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
	}
	
	
	/**
	 * Loads a member's Flickr username, given the member ID.
	 *
	 * @access	public
	 * @param	int|string		$member_id		The member ID.
	 * @return	string
	 */
	public function get_member_flickr_username($member_id = '')
	{
		// Shortcuts.
		$db =& $this->_ee->db;
		
		$db_member = $db->select('m_field_id_10')->get_where('member_data', array('member_id' => $member_id));
		
		return $db_member->num_rows() === 1
			? $db_member->row()->m_field_id_10
			: '';
	}
	
}

/* End of file 		: pickr_model.php */
/* File location	: third_party/pickr/models/pickr_model.php */