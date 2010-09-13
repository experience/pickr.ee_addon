<?php

/**
 * Tests for the Pickr extension.
 *
 * @package		Pickr
 * @author 		Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright 	Experience Internet
 */

require_once PATH_THIRD .'pickr/ext.pickr' .EXT;
require_once PATH_THIRD .'pickr/tests/mocks/mock_pickr_flickr' .EXT;
require_once PATH_THIRD .'pickr/tests/mocks/mock_pickr_model' .EXT;

class Test_pickr_ext extends Testee_unit_test_case {
	
	/* --------------------------------------------------------------
	 * PRIVATE PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * Pickr extension.
	 *
	 * @access	private
	 * @var		Pickr_ext
	 */
	private $_ext;
	
	/**
	 * Pickr model (mock).
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
		
		// Mocks.
		Mock::generate('Mock_pickr_model', 'Pickr_model');
		Mock::generate('Mock_pickr_flickr', 'Pickr_flickr');
		
		$this->_model	= new Pickr_model();
		$this->_ext 	= new Pickr_ext(array(), $this->_model);
	}
	
	
	/* --------------------------------------------------------------
	 * TEST METHODS
	 * ------------------------------------------------------------ */

	public function test_activate_extension()
	{
		$this->_model->expectOnce('activate_extension');
		$this->_ext->activate_extension();
	}
	
	
	public function test_disable_extension()
	{
		$this->_model->expectOnce('disable_extension');
		$this->_ext->disable_extension();
	}
	
	
	public function test_update_extension()
	{
		$model = $this->_model;
		$version = '1.0.0';
		
		$model->expect('get_package_version', array());
		$model->setReturnValue('get_package_version', $version);
		
		$model->expect('update_extension', array($version, $version));
		$model->setReturnValue('update_extension', FALSE, array($version, $version));
		
		// Run the test.
		$this->assertIdentical($this->_ext->update_extension($version), FALSE);
	}
	
	
	public function test_on_member_register_validate_members()
	{
		$model 		= $this->_model;
		$member_id	= '10';
		$credentials = array('api_key' => '1234567890', 'secret_key' => 'ssshhh');
		
		$model->expectOnce('get_api_credentials');
		$model->setReturnValue('get_api_credentials', $credentials);
		$model->expectOnce('set_api_connector', array(new Pickr_flickr($credentials['api_key'], $credentials['secret_key'])));
		$model->expectOnce('get_member_flickr_buddy_icon', array($member_id));
		
		$this->_ext->on_member_register_validate_members($member_id);
	}
	
}


/* End of file 		: test_pickr_ext.php */
/* File location	: third_party/pickr/tests/test_pickr_ext.php */