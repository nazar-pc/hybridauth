<?php
/*!
* HybridAuth
* http://hybridauth.github.io | http://github.com/hybridauth/hybridauth
* (c) 2015 HybridAuth authors | http://hybridauth.github.io/license.html
*/

namespace Hybridauth\Provider;

use Hybridauth\Adapter\OAuth1;
use Hybridauth\Exception\UnexpectedValueException;
use Hybridauth\Data;
use Hybridauth\User;

class Tumblr extends OAuth1
{
    /**
    * {@inheritdoc}
    */
    protected $apiBaseUrl = 'http://api.tumblr.com/v2/';

    /**
    * {@inheritdoc}
    */
    protected $authorizeUrl = 'http://www.tumblr.com/oauth/authorize';

    /**
    * {@inheritdoc}
    */
    protected $requestTokenUrl = 'http://www.tumblr.com/oauth/request_token';

    /**
    * {@inheritdoc}
    */
    protected $accessTokenUrl = 'http://www.tumblr.com/oauth/access_token';

    /**
    * {@inheritdoc}
    */
    public function getUserProfile()
    {
        $response = $this->apiRequest('user/info');

        $data = new Data\Collection($response);

        if (! $data->exists('response')) {
            throw new UnexpectedValueException('Provider API returned an unexpected response.');
        }

        $userProfile = new User\Profile();

        $userProfile->displayName = $data->filter('response')->filter('user')->get('name');

        foreach ($data->filter('response')->filter('user')->filter('blogs')->all() as $blog) {
            if ($blog->get('primary') && $blog->exists('url')) {
                $userProfile->identifier  = $blog->get('url');
                $userProfile->profileURL  = $blog->get('url');
                $userProfile->webSiteURL  = $blog->get('url');
                $userProfile->description = strip_tags($blog->get('description'));

                $bloghostname = explode('://', $blog->get('url'));
                $bloghostname = substr($bloghostname[1], 0, -1);

                $this->token('primary_blog', $bloghostname);

                break;
            }
        }

        return $userProfile;
    }

    /**
    * {@inheritdoc}
    */
    public function setUserStatus($status)
    {
        $status = is_string($status)
                    ? [ 'type' => 'text', 'body' => $status ]
                    : $status;

        $response = $this->apiRequest('blog/' . $this->token('primary_blog') . '/post', 'POST', $status);

        return $response;
    }
}
