<?php if ( ! defined('BASEPATH')) exit('Direct script access is not permitted.');

/**
 * Retrieves the Flickr 'buddy icon' for a newly-registered member.
 *
 * @package			Pickr
 * @author 			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright 		Experience Internet
 */

class Pickr_ext {
	
	/* --------------------------------------------------------------
	 * PRIVATE PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * ExpressionEngine object reference.
	 *
	 * @access	private
	 * @var		object
	 */
	private $_ee;
	
	/**
	 * Model.
	 *
	 * @access	private
	 * @var		Pickr_model
	 */
	private $_model;
	
	
	/* --------------------------------------------------------------
	 * PUBLIC PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * Description.
	 *
	 * @access	public
	 * @var		string
	 */
	public $description;
	
	/**
	 * Documentation URL.
	 *
	 * @access	public
	 * @var		string
	 */
	public $docs_url;
	
	/**
	 * Extension name.
	 *
	 * @access	public
	 * @var		string
	 */
	public $name;
	
	/**
	 * Does this extension have a settings screen?
	 *
	 * @access	public
	 * @var		string
	 */
	public $settings_exist = 'n';
	
	/**
	 * Version.
	 *
	 * @access	public
	 * @var		string
	 */
	public $version;
	
	
	
	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */

	/**
	 * Class constructor.
	 *
	 * @access	public
	 * @param	array 			$settings		Previously-saved extension settings.
	 * @param 	Pickr_model		$model			Model. Passed directly to constructor during testing.
	 * @return	void
	 */
	public function __construct($settings = array(), Pickr_model $model = NULL)
	{
		$this->_ee =& get_instance();
		
		// Load the model, if required.
		if ($model)
		{
			$this->_model = $model;
		}
		else
		{
			$this->_ee->load->add_package_path(PATH_THIRD .'pickr/');
			$this->_ee->load->model('pickr_model');
			
			$this->_model = $this->_ee->pickr_model;
		}
		
		// Load the language file.
		$this->_ee->lang->loadfile('pickr');
		
		// Set the instance properties.
		$this->description	= $this->_ee->lang->line('extension_description');
		$this->docs_url		= 'http://experienceinternet.co.uk/software/pickr/';
		$this->name			= $this->_ee->lang->line('extension_name');
		$this->version		= $this->_model->get_package_version();
	}
	
	
	/**
	 * Activates the extension.
	 *
	 * @access	public
	 * @return	void
	 */
	public function activate_extension()
	{
		$this->_model->activate_extension();
	}
	
	
	/**
	 * Disables the extension.
	 *
	 * @access	public
	 * @return	void
	 */
	public function disable_extension()
	{
		$this->_model->disable_extension();
	}
	
	
	/**
	 * Updates the extension.
	 *
	 * @access	public
	 * @param	string		$current_version	The current version.
	 * @return	bool|void
	 */
	public function update_extension($current_version = '')
	{
		return $this->_model->update_extension($current_version, $this->_model->get_package_version());
	}
	
	
	
	/* --------------------------------------------------------------
	 * HOOK HANDLERS
	 * ------------------------------------------------------------ */

	/**
	 * Handles the `member_register_validate_members` hook.
	 *
	 * @see		http://expressionengine.com/developers/extension_hooks/member_register_validate_members/
	 * @access	public
	 * @param 	string 		$member_id		The ID of the newly validated member.
	 * @return	void
	 */
	public function on_member_register_validate_members($member_id)
	{
		try
		{
			$this->_model->get_member_flickr_buddy_icon($member_id);
		}
		catch (Pickr_exception $e)
		{
			// Do nothing. Maybe log something in the future.
		}
	}
	
}

/* End of file		: ext.pickr.php */
/* File location	: third_party/pickr/ext.pickr.php */