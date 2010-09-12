<?php

/**
 * Pickr_flickr mock.
 *
 * @package			Pickr
 * @author 			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright 		Experience Internet
 * @version 		0.1.0
 */

class Mock_pickr_flickr {
	
	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */
	public function people_find_by_username() {}
	
	/*
		1: User not found
		No user with the supplied username was found.
		100: Invalid API Key
		The API key passed was not valid or has expired.
		105: Service currently unavailable
		The requested service is temporarily unavailable.
		111: Format "xxx" not found
		The requested response format was not found.
		112: Method "xxx" not found
		The requested method was not found.
		114: Invalid SOAP envelope
		The SOAP envelope send in the request could not be parsed.
		115: Invalid XML-RPC Method Call
		The XML-RPC request document could not be parsed.
		116: Bad URL found
		One or more arguments contained a URL that has been used for abuse on Flickr.
	*/
	
	public function people_get_public_photos() {}
	
	/*
		1: User not found
		The user NSID passed was not a valid user NSID.
		100: Invalid API Key
		The API key passed was not valid or has expired.
		105: Service currently unavailable
		The requested service is temporarily unavailable.
		111: Format "xxx" not found
		The requested response format was not found.
		112: Method "xxx" not found
		The requested method was not found.
		114: Invalid SOAP envelope
		The SOAP envelope send in the request could not be parsed.
		115: Invalid XML-RPC Method Call
		The XML-RPC request document could not be parsed.
		116: Bad URL found
		One or more arguments contained a URL that has been used for abuse on Flickr.
	*/
}

/* End of file 		: mock_pickr_flickr.php */
/* File location	: third_party/pickr/tests/mocks/mock_pickr_flickr.php */