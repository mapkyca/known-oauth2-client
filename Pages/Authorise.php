<?php

namespace IdnoPlugins\OAuth2Client\Pages;

use Idno\Core\Idno;
use IdnoPlugins\OAuth2Client\Entities\OAuth2ClientException;

class Authorise extends \Idno\Common\Page {

    function getContent() {
    
	if (!empty($this->arguments[0])) {
	    $object = \Idno\Common\Entity::getByID($this->arguments[0]);
	}
	
	if (empty($object)) throw new \RuntimeException(Idno::site ()->language ()->_('Could not find client'));
	
	$provider = new \League\OAuth2\Client\Provider\GenericProvider([
	    'clientId'                => $object->client_id,
	    'clientSecret'            => $object->client_select,
	    'redirectUri'             => $object->getURL(), //$object->redirect_uri,
	    'urlAuthorize'            => $object->url_authorise,
	    'urlAccessToken'          => $object->url_access_token,
	    'urlResourceOwnerDetails' => $object->url_resource
	]);

	if (!$this->getInput('code')) {
	    // Fetch the authorization URL from the provider; this returns the
	    // urlAuthorize option and generates and applies any necessary parameters
	    // (e.g. state).
	    $authorizationUrl = $provider->getAuthorizationUrl();

	    // Get the state generated for you and store it to the session.
	    $_SESSION['oauth2state'] = $provider->getState();

	    // Redirect the user to the authorization URL.
	    $this->forward($authorizationUrl);
	    
/*	} else if (empty($this->getInput('state')) || (isset($_SESSION['oauth2state']) && $this->getInput('state') !== $_SESSION['oauth2state'])) {

	    if (isset($_SESSION['oauth2state'])) {
		unset($_SESSION['oauth2state']);
	    }

	    throw new \RuntimeException(Idno::site()->language()->_('Invalid state'));
*/
	} else {
	    
	    // Try to get an access token using the authorization code grant.
	    $accessToken = $provider->getAccessToken('authorization_code', [
		'code' => $this->getInput('code')
	    ]);
	    
	    $details = [
		'access_token' => $accessToken,
		'owner_resource' => $provider->getResourceOwner($accessToken)
	    ];
            
	    Idno::site()->logging()->info(var_export($details, true));
	    $user = Idno::site()->events()->triggerEvent('oauth2/authorised', $details);
	    
            if ($user && $user instanceof Idno\Entities\User) {
                
                \Idno\Core\Idno::site()->session()->logUserOn($user);
                
                $this->forward($user->getUrl());
            } else {
		
		// Ok, now see if we can do a default log-in
		$id = null;
		$username = null;
		$name = null;
		if (!empty($details['owner_resource']->toArray()['id'])) {
		    $id = $details['owner_resource']->toArray()['id'];
		}
		if (!empty($details['owner_resource']->toArray()['username'])) {
		    $name = $username = $details['owner_resource']->toArray()['username'];
		}
                if (!empty($details['owner_resource']->toArray()['name'])) {
		    $name = $details['owner_resource']->toArray()['name'];
		}
		
		if ($id || $username) {
		
		    $user = \Idno\Entities\User::get(['oauth2_userid' => $id])[0];
		    if (!$user)
			$user = \Idno\Entities\User::get(['oauth2_username' => $username])[0];
		    
		    if (!$user) {
		
			$user = new \Idno\Entities\User();
                        $user->title = $name;
			$user->email  = $details['owner_resource']->toArray()['email'];
			$user->handle = $username ? $username : $id;
			$user->setPassword(sha1(rand()));
			$user->notifications['email'] = 'all';
			
			$user->oauth2_userid = $id;
			$user->oauth2_username = $username;
			
			if ($user->save())
			    $this->forward($user->getURL ());
		    }
		    
		    \Idno\Core\Idno::site()->session()->logUserOn($user);
                
		    $this->forward($user->getUrl());
		
		} else
		    throw new OAuth2ClientException(Idno::site()->language()->_('Could not find a suitable handler for this user data.'));
            }
	    
            
	    
	}
	
	
    }

}
