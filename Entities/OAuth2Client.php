<?php


namespace IdnoPlugins\OAuth2Client\Entities;

use Idno\Core\Idno;

class OAuth2Client extends \Idno\Entities\BaseObject {
    
    public function getURL() {
	return \Idno\Core\Idno::site()->config()->getDisplayURL() . 'oauth2/authorise/' . $this->getID();
    }
    
    public function getEditURL(): string {
	return \Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/oauth2client/' . $this->getID();
    }
    
    public function saveDataFromInput() {
	
	if (empty($this->_id)) {
	    $new = true;
	} else {
	    $new = false;
	}
	
	// Save variables
	foreach ([
	    'label', 'client_id', 'client_secret', 'redirect_uri', 'url_authorise', 'url_access_token', 'url_resource', 'scopes', 'publickey_url'
	] as $input) {
	    
	    $this->$input = \Idno\Core\Idno::site()->currentPage()->getInput($input);	
	    
	}
        
        if (!empty($this->publickey_url)) {
            
            $publickey = \Idno\Core\Webservice::file_get_contents($this->publickey_url);
            
            if (empty($publickey)) {
                \Idno\Core\site()->session()->addErrorMessage(Idno::site()->language()->_('Public key could not be retrieved from %s', [$this->publickey_url]));
            }
            
            $this->publickey = $publickey;
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
	
	return $this->save();
    }
    
    
}