<?php

/**
 * Tests for the Pickr model.
 *
 * @package			Pickr
 * @author 			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright 		Experience Internet
 */

require_once PATH_THIRD .'pickr/models/pickr_model' .EXT;
require_once PATH_THIRD .'pickr/tests/mocks/mock_pickr_flickr' .EXT;

class Test_pickr_model extends Testee_unit_test_case {
	
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
		
		// Mock the API connector object.
		Mock::generate('Mock_pickr_flickr', 'Pickr_flickr');
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
		$this->assertIdentical($model->get_member_flickr_username('100'), '');
	}
	
	
	public function test_get_member_flickr_username__invalid_member_id()
	{
		$this->assertIdentical($this->_ee->pickr_model->get_member_flickr_username('NULL'), '');
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
		$this->assertIdentical($this->_ee->pickr_model->save_member_flickr_photo('', ''), FALSE);
	}
	
	
	public function test_save_member_flickr_photo__invalid_member_id()
	{
		$this->assertIdentical($this->_ee->pickr_model->save_member_flickr_photo('NULL', ''), FALSE);
	}
	
	
	public function test_get_flickr_nsid_from_username__pass()
	{
		$model	= $this->_ee->pickr_model;
		$conn	= new Pickr_flickr();			// Mock object.
		
		$flickr_username = 'wibble';
		$flickr_nsid = '12345678@N00';
		
		$flickr_user = array(
			'stat' => 'ok',
			'user' => array(
				'id'		=> $flickr_nsid,
				'nsid'		=> $flickr_nsid,
				'username'	=> array('_content' => $flickr_username)
			)
		);
		
		$conn->expectOnce('people_find_by_username', array($flickr_username));
		$conn->setReturnReference('people_find_by_username', $flickr_user, array($flickr_username));
		
		// Set model API connector.
		$model->set_api_connector(&$conn);
		
		// Run the tests.
		$this->assertIdentical($model->get_flickr_nsid_from_username($flickr_username), $flickr_nsid);
	}
	
	
	public function test_get_flickr_nsid_from_username__no_credentials()
	{
		$model	= $this->_ee->pickr_model;
		$conn	= new Pickr_flickr();
		
		$conn->throwOn('people_find_by_username', new Pickr_api_exception('API credentials not set.'));
		$model->set_api_connector(&$conn);
		
		// Run the test.
		$this->expectException(new Pickr_api_exception('API credentials not set.'));
		$model->get_flickr_nsid_from_username('NULL');
	}
	
	
	public function test_get_flickr_nsid_from_username__api_exception()
	{
		$model		= $this->_ee->pickr_model;
		$conn		= new Pickr_flickr();
		$exception	= new Pickr_api_exception('User not found', '1');
		
		$conn->throwOn('people_find_by_username', $exception);
		$model->set_api_connector(&$conn);
		
		// Run the test.
		$this->expectException($exception);
		$model->get_flickr_nsid_from_username('wibble');
	}
	
	
	public function test_get_flickr_user_info_from_nsid__pass()
	{
		$model	= $this->_ee->pickr_model;
		$conn	= new Pickr_flickr();
		
		$flickr_nsid = '123456';
		$flickr_info = array('example' => 'example');
		
		$conn->expectOnce('people_get_info', array($flickr_nsid));
		$conn->setReturnReference('people_get_info', $flickr_info, array($flickr_nsid));
		
		// Set the model API connector.
		$model->set_api_connector(&$conn);
		
		// Run the tests.
		$this->assertIdentical($model->get_flickr_user_info_from_nsid($flickr_nsid), $flickr_info);
	}
	
}

/* End of file 		: test_pickr_model.php */
/* File location	: third_party/pickr/tests/test_pickr_model.php */