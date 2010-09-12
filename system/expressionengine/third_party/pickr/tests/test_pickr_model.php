<?php

/**
 * Tests for the Pickr model.
 *
 * @package			Pickr
 * @author 			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright 		Experience Internet
 */

require_once PATH_THIRD .'pickr/models/pickr_model' .EXT;

class Test_pickr_model extends Testee_unit_test_case {
	
	/* --------------------------------------------------------------
	 * PRIVATE PROPERTIES
	 * ------------------------------------------------------------ */
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
		$this->_ee->load->model('pickr_model');
	}
	
	
	/* --------------------------------------------------------------
	 * TEST METHODS
	 * ------------------------------------------------------------ */
	
	public function test_get_member_flickr_username__pass()
	{
		// Shortcuts.
		$db 	=& $this->_ee->db;
		$model	= $this->_ee->pickr_model;
		
		// Dummy values.
		$flickr_username 	= 'wibble';
		$member_id 			= '5';
		$member_field_id	= $model->get_flickr_username_member_field_id();
		
		// Query row.
		$db_row = new StdClass();
		$db_row->$member_field_id = $flickr_username;
		
		// Query result.
		$db_result = $this->_get_mock('query');
		$db_result->expectOnce('row');
		$db_result->setReturnValue('num_rows', 1);
		$db_result->setReturnReference('row', $db_row);
		
		// Database.
		$db->expectOnce('select', array($member_field_id));
		$db->expectOnce('get_where', array('member_data', array('member_id' => $member_id)));
		
		$db->setReturnReference('select', $db);
		$db->setReturnReference('get_where', $db_result);
		
		// Run the tests.
		$this->assertIdentical($model->get_member_flickr_username($member_id), $flickr_username);
	}
	
	
	public function test_get_member_flickr_username__unknown_member()
	{
		// Shortcuts.
		$db 	=& $this->_ee->db;
		$model	= $this->_ee->pickr_model;
		
		// Query result.
		$db_result = $this->_get_mock('query');
		$db_result->setReturnValue('num_rows', 0);
		
		// Database.
		$db->setReturnReference('select', $db);
		$db->setReturnReference('get_where', $db_result);
		
		// Run the tests.
		$this->assertIdentical($model->get_member_flickr_username('NULL'), '');
	}
	
	
	public function test_save_member_flickr_photo__pass()
	{
		// Shortcuts.
		$db		=& $this->_ee->db;
		$model	= $this->_ee->pickr_model;
		
		// Dummy values.
		$field_id	= $model->get_flickr_photo_member_field_id();
		$member_id	= '5';
		$photo_url	= 'http://myphoto.com/';
		
		// Query.
		$data 	= array($field_id => $photo_url);
		$where	= array('member_id' => $member_id);
		
		$db->expectOnce('update', array('member_data', $data, $where));
		$db->expectOnce('affected_rows');
		$db->setReturnValue('affected_rows', 1);
		
		// Run the tests.
		$this->assertIdentical($model->save_member_flickr_photo($member_id, $photo_url), TRUE);
	}
	
	
	public function test_save_member_flickr_photo__not_saved()
	{
		$this->_ee->db->setReturnValue('affected_rows', 0);
		
		// Run the tests.
		$this->assertIdentical($this->_ee->pickr_model->save_member_flickr_photo('', ''), FALSE);
	}
	
}

/* End of file 		: test_pickr_model.php */
/* File location	: third_party/pickr/tests/test_pickr_model.php */