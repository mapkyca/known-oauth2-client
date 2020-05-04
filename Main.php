<?php

namespace IdnoPlugins\OAuth2Client;

use IdnoPlugins\OAuth2\OIDCToken; // Naughty, but I'll decouple later
use Idno\Core\Idno;
use IdnoPlugins\OAuth2Client\Entities\OAuth2ClientException;

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
    
    function registerEventHooks()
    {

        // Authenticate!
        \Idno\Core\site()->events()->addListener('user/auth/request', function(\Idno\Core\Event $event) {

            if ($user = Main::authenticate()) {
                $event->setResponse($user);
            }

        }, 1);
    }

    /**
     * Support federation via OIDC
     * @return \Idno\Entities\RemoteUser
     */
    public static function authenticate()
    {
        $access_token = \Idno\Core\Input::getInput('access_token');
        if (!$access_token)
            $access_token = \Idno\Common\Page::getBearerToken ();

        // Have we been provided with an access token
        if ($access_token) {

            \Idno\Core\Idno::site()->session()->setIsAPIRequest(true);

            // Validate bearer if it's a JWT/OIDC
            if (OIDCToken::isJWT($access_token)) {
                
                // Preliminary decode - peek at the OIDC, to see if we can find the client
                $unsafejwt = OIDCToken::decodeNoVerify($access_token);

                if (!empty($unsafejwt->aud)) {

                    // Can we find a client for this
                    $client = Entities\OAuth2Client::getOne(['client_id' => $unsafejwt->aud]);
                    if (!empty($client) && !empty($client->publickey)) {

                        // Have we federated with this site?
                        if ($client->federation) {

                            // Now, lets validate.
                            $safejwt = OIDCToken::decode($access_token, $client->publickey);

                            // Ok, we got here, so the OIDC token is valid, lets find a user
                            if (!empty($safejwt)) {

                                $id = $safejwt->sub;

                                // Try a local user
                                $user = \Idno\Entities\User::getOne(['oauth2_userid' => $safejwt->aud . '_' . $id]);

                                // Try a remote user
                                if (empty($user)) {
                                    $user = \Idno\Entities\RemoteUser::getOne(['oauth2_userid' => $safejwt->aud . '_' . $id]);
                                }

                                // Nothing, create this new remote user
                                if (empty($user)) {

                                    $user = new \Idno\Entities\RemoteUser();

                                    $user->title = $safejwt->name ?? "OIDC User {$id}";
                                    $user->email = $safejwt->email ?? '';
                                    $user->handle = $safejwt->preferred_username ?? $id;
                                    if (!empty($safejwt->profile)) $user->url = $safejwt->profile;
                                    if (!empty($safejwt->picture)) $user->image = $safejwt->picture ?? '';

                                    $user->oauth2_userid = $safejwt->aud . '_' . $id;
                                    
                                    if (!$user->save()) {
                                        throw new OAuth2ClientException(Idno::site()->language()->_('New user account could not be saved'));
                                    }
                                }

                                if (!empty($user)) {
                                    \Idno\Core\site()->session()->refreshSessionUser($user); // Log user on, but avoid triggering hook and going into an infinite loop!

                                    return $user;
                                }

                            } 


                        } 

                    } 

                } 

            } 
        }
    }
}
