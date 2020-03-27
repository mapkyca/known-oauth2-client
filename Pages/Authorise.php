<?php

namespace IdnoPlugins\OAuth2Client\Pages;

use Idno\Core\Idno;
use IdnoPlugins\OAuth2Client\Entities\OAuth2ClientException;

class Authorise extends \Idno\Common\Page {

    function getContent() {

        if (!empty($this->arguments[0])) {
            $object = \Idno\Common\Entity::getByID($this->arguments[0]);
        }

        if (empty($object))
            throw new \RuntimeException(Idno::site()->language()->_('Could not find client'));

        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => $object->client_id,
            'clientSecret' => $object->client_secret,
            'redirectUri' => $object->getURL(), //$object->redirect_uri,
            'urlAuthorize' => $object->url_authorise,
            'urlAccessToken' => $object->url_access_token,
            'urlResourceOwnerDetails' => $object->url_resource,
            'scopes' => $object->scopes,
            'scopesSeparator' => ' '
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
                
                $id = null;
                $username = null;
                $name = null;
                $email = null;
                
                // Ok, lets see if we have an OIDC token
                $values = $accessToken->getValues();
                if (!empty($values['id_token'])) {
                    
                    $jwt = $values['id_token'];
                    list($header, $payload, $signature) = explode(".", $jwt);

                    $plainHeader = base64_decode($header);
                    $jsonHeader = json_decode($plainHeader, true);
                    $plainPayload = base64_decode($payload);
                    $jsonPayload = json_decode($plainPayload, true);
                    
                    if (!empty($jsonPayload['preferred_username'])) {
                        $name = $username = $jsonPayload['preferred_username'];
                    }
                    
                    if (!empty($jsonPayload['email'])) {
                        $email = $jsonPayload['email'];
                    }
                    
                    if (!empty($jsonPayload['name'])) {
                        $name = $jsonPayload['name'];
                    }
                    
                    if (!empty($jsonPayload['sub'])) {
                        $id = $jsonPayload['sub'];
                    }
                }
                
                // Ok, now see if we can do a default log-in   
                if (empty($id) && !empty($details['owner_resource']->toArray()['id'])) {
                    $id = $details['owner_resource']->toArray()['id'];
                }
                if (empty($username) && !empty($details['owner_resource']->toArray()['username'])) {
                    $name = $username = $details['owner_resource']->toArray()['username'];
                }
                if (empty($name) && !empty($details['owner_resource']->toArray()['name'])) {
                    $name = $details['owner_resource']->toArray()['name'];
                }
                if (empty($email) && !empty($details['owner_resource']->toArray()['email'])) {
                    $email = $details['owner_resource']->toArray()['email'];
                }

                if ($id || $username) {

                    $user = \Idno\Entities\User::get(['oauth2_userid' => $object->client_id . '_' . $id])[0];
                    if (!$user) {
                        $user = \Idno\Entities\User::get(['oauth2_username' => $object->client_id . '_' . $username])[0];
                    }

                    if (!$user) {

                        $user = new \Idno\Entities\User();
                        $user->title = $name;
                        $user->email = $email;
                        $user->handle = $username ? $username : $id;
                        $user->setPassword(sha1(rand()));
                        $user->notifications['email'] = 'all';

                        $user->oauth2_userid = $id;
                        $user->oauth2_username = $username;

                        if ($user->save())
                            $this->forward($user->getURL());
                    }

                    \Idno\Core\Idno::site()->session()->logUserOn($user);

                    $this->forward($user->getUrl());
                } else
                    throw new OAuth2ClientException(Idno::site()->language()->_('Could not find a suitable handler for this user data.'));
            }
        }
    }

}
