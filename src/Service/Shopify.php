<?php

namespace App\Service;

use PHPShopify\ShopifySDK;
use PHPShopify\AuthHelper;

class Shopify 
{
    protected $scopes;
    protected $redirectUrl;
    protected $apiKey;
    protected $secret;

    public function __construct($apiKey, $secret, $scopes, $redirectUrl)
    {
        $this->apiKey = $apiKey;
        $this->secret = $secret;
        $this->scopes = $scopes;
        $this->redirectUrl = $redirectUrl;
    }

    public function getApiKey() 
    {
        return $this->apiKey;
    }

    public function getSecret() 
    {
        return $this->secret;
    }

    public function generateAuthUrl($shopUrl)
    {
        ShopifySDK::config([
            'ShopUrl' => $shopUrl,
            'ApiKey' => $this->apiKey,
            'SharedSecret' => $this->secret,
        ]);
        
        return AuthHelper::createAuthRequest($this->scopes, $this->redirectUrl, null, null, true);
    }

    public function getAccessToken($shopUrl)
    {        
        ShopifySDK::config([
            'ShopUrl' => $shopUrl,
            'ApiKey' => $this->apiKey,
            'SharedSecret' => $this->secret,
        ]);

        return AuthHelper::getAccessToken();
    }

    public function verifyHmac($query) 
    {
        $params = [];
    
        foreach ($query as $param => $value) {
            if ($param != 'signature' && $param != 'hmac') {
                $params[$param] = "{$param}={$value}";
            }
        }
    
        asort($params);
    
        $params = implode('&', $params);
        $hmac = $query['hmac'];
        $calculatedHmac = hash_hmac('sha256', $params, $this->secret);

        return $hmac == $calculatedHmac;
    }
}