<?php


namespace IdnoPlugins\OAuth2Client\Entities;

class Client extends \Idno\Entities\BaseObject {
    
    public function getEditURL(): string {
	return \Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/oauth2client/' . $this->getID();
    }
    
    public function saveDataFromInput() {
	
	$key = 'new';
	
	if (empty($this->_id)) {
	    $new = true;
	    $key = $this->_id;
	} else {
	    $new = false;
	}
	
	// Save variables
	foreach ([
	    'label', 'client_id', 'client_secret', 'redirect_uri', 'url_authorise', 'url_access_token', 'url_resource'
	] as $input) {
	    $this->$input = \Idno\Core\Idno::site()->currentPage()->getInput($input);	    
	}
	
	// Save button
	if ($file = \Idno\Core\Input::getFiles('signin_button')) {
	    
	    if (!empty($file['tmp_name'])) {

		if (\Idno\Entities\File::isImage($file['tmp_name']) || \Idno\Entities\File::isSVG($file['tmp_name'], $file['name'])) {
		    
		    if ($button = \Idno\Entities\File::createFromFile($file['tmp_name'], $file['name'], $file['type'], true, true)) {
			$this->attachFile($button);
		    }
		}
		
	    }

	    
	}
	
	return true;
    }
    
    
}