<?php

namespace IdnoPlugins\OAuth2Client\Pages;

use Idno\Core\Idno;

class Authorise extends \Idno\Common\Page {

    function getContent() {
    
	if (!empty($this->arguments[0])) {
	    $object = \Idno\Common\Entity::getByID($this->arguments[0]);
	}
	
	
	
	
	
    }

}
