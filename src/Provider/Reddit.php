<?php
/*!
* HybridAuth
* http://hybridauth.github.io | http://github.com/hybridauth/hybridauth
* (c) 2015 HybridAuth authors | http://hybridauth.github.io/license.html
*/

namespace Hybridauth\Provider;

use Hybridauth\Adapter\OAuth2;
use Hybridauth\Exception\UnexpectedValueException;
use Hybridauth\Data;
use Hybridauth\User;

/**
 * Reddit
 *
 * http://www.reddit.com/dev/api/oauth
 * https://github.com/reddit/reddit/wiki/OAuth2
 */
class Reddit extends OAuth2
{
    /**
    * {@inheritdoc}
    */
    protected $scope = 'identity';

    /**
    * {@inheritdoc}
    */
    protected $apiBaseUrl = 'https://oauth.reddit.com/api/v1/';

    /**
    * {@inheritdoc}
    */
    protected $authorizeUrl = 'https://ssl.reddit.com/api/v1/authorize';

    /**
    * {@inheritdoc}
    */
    protected $accessTokenUrl = 'https://ssl.reddit.com/api/v1/access_token';

    /**
    * {@inheritdoc}
    */
    protected function initialize()
    {
        parent::initialize();

        $this->tokenExchangeParameters = [
            'client_id'    => $this->clientId,
            'grant_type'   => 'authorization_code',
            'redirect_uri' => $this->endpoint
        ];

        $this->tokenExchangeHeaders = [
            'Authorization' => 'Basic ' . base64_encode($this->clientId .  ':' . $this->clientSecret)
        ];

        $this->apiRequestHeaders = [
            'Authorization' => 'Bearer ' . $this->token('access_token')
        ];
    }

    /**
    * {@inheritdoc}
    */
    protected function getAuthorizeUrl($parameters = [])
    {
        $addtionals = [
            'duration' => 'temporary'
        ];

        $parameters = array_replace($parameters, (array) $addtionals);

        return parent::getAuthorizeUrl($parameters);
    }

    /**
    * {@inheritdoc}
    */
    public function getUserProfile()
    {
        $response = $this->apiRequest('me.json');

        $data = new Data\Collection($response);

        if (! $data->exists('id')) {
            throw new UnexpectedValueException('Provider API returned an unexpected response.');
        }

        $userProfile = new User\Profile();

        $userProfile->identifier  = $data->get('id');
        $userProfile->displayName = $data->get('name');
        $userProfile->profileURL  = 'https://www.reddit.com/user/' . $data->get('name') . '/';

        return $userProfile;
    }
}
