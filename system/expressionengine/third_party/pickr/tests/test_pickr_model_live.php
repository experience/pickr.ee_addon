<?php

/**
 * Tests for the Pickr model.
 *
 * @package			Pickr
 * @author 			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright 		Experience Internet
 */

require_once PATH_THIRD .'pickr/models/pickr_model' .EXT;
require_once PATH_THIRD .'pickr/libraries/pickr_flickr' .EXT;

class Test_pickr_model_live extends Testee_unit_test_case {
	
	/* --------------------------------------------------------------
	 * PRIVATE PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * Pickr model.
	 *
	 * @access	private
	 * @var		Pickr_model
	 */
	private $_model;
	
	
	
	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Runs before each test.
	 *
	 * @access	public
	 * @return	void
	 */
	public function setUp()
	{
		parent::setUp();
		
		// Doing this ensures a fresh model for each test.
		$this->_model = new Pickr_model();
	}
	
	
	/* --------------------------------------------------------------
	 * TEST METHODS
	 * ------------------------------------------------------------ */
	
	public function test_get_member_flickr_buddy_icon()
	{
		// Shortcuts.
		$db 	= $this->_ee->db;
		$model	= $this->_model;
		
		$credentials	= $this->_model->get_api_credentials();
		$connector		= new Pickr_flickr($credentials['api_key'], $credentials['secret_key']);
		
		// Dummy values.
		$flickr_username 	= 'brettwalker';		// Hard-coded, known ID.
		$member_id 			= '2';
		$member_field_id	= 'm_field_id_10';
		
		// Config.
		$this->_ee->config->setReturnValue('item', $member_field_id, array('flickr_username_member_field'));
		
		// Query row.
		$db_row = new StdClass();
		$db_row->$member_field_id = $flickr_username;
		
		// Query result.
		$db_result = $this->_get_mock('db_query');
		$db_result->setReturnValue('num_rows', 1);
		$db_result->setReturnReference('row', $db_row);
		
		// Database.
		$db->setReturnReference('select', $db);
		$db->setReturnReference('get_where', $db_result);
		
		$model->set_api_connector($connector);
		$model->get_member_flickr_buddy_icon($member_id);
	}
}

/* End of file 		: test_pickr_model.php */
/* File location	: third_party/pickr/tests/test_pickr_model.php */