<?php

namespace IdnoPlugins\OAuth2Client;

class Main extends \Idno\Common\Plugin {
    
    function init() {
	
	// Use autoload, if it's not centrally loaded.
	if (file_exists(dirname(__FILE__) . '/vendor/autoload.php'))
	{
	    require_once(dirname(__FILE__) . '/vendor/autoload.php');
	}
	
	parent::init();
    }
    
    function registerPages()
    {
	// Register admin settings
	\Idno\Core\Idno::site()->routes()->addRoute('admin/oauth2client', '\IdnoPlugins\OAuth2Client\Pages\Admin');
	
	// Add menu items to account & administration screens
	\Idno\Core\site()->template()->extendTemplate('admin/menu/items', 'admin/oauth2client/menu');
	
    }

    function registerTranslations() {
        \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                        'oauth2client', dirname(__FILE__) . '/languages/'
                )
        );
    }
    
    function registerEventHooks() {
        
    }

}
