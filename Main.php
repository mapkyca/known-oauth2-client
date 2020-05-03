<?php

namespace IdnoPlugins\OAuth2Client;

class Main extends \Idno\Common\Plugin {
    
    function registerPages()
    {
	// Register admin settings
	\Idno\Core\Idno::site()->routes()->addRoute('admin/oauth2client/?', '\IdnoPlugins\OAuth2Client\Pages\Admin');
	\Idno\Core\Idno::site()->routes()->addRoute('admin/oauth2client/([A-Za-z0-9]+)/?', '\IdnoPlugins\OAuth2Client\Pages\Admin');
	
	// Add menu items to account & administration screens
	\Idno\Core\site()->template()->extendTemplate('admin/menu/items', 'admin/oauth2client/menu');
	
	// Register admin settings
	\Idno\Core\Idno::site()->routes()->addRoute('oauth2/authorise/([A-Za-z0-9]+)/?', '\IdnoPlugins\OAuth2Client\Pages\Authorise');
	
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
