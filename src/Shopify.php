<?php

namespace Kmrifat\Shopify;

use Kmrifat\Shopify\Services\Graph\GraphService;
use Kmrifat\Shopify\Services\Rest\RESTService;

/**
 * Class Shopify
 *
 * @package Kmrifat\Shopify
 */
class Shopify
{

    private string $shopDomain;
    private string $accessToken;

    /**
     * Shop Register by shop domain and access token
     *
     * @param $shopDomain
     * @param $accessToken
     * @return $this
     */
    public function shop($shopDomain, $accessToken): static
    {
        $this->accessToken = $accessToken;
        $this->shopDomain = $shopDomain;
        return $this;
    }

    /**
     * Shopify Rest API Service
     * Support GET, POST, PUT, DELETE
     * @return RESTService
     */
    public function rest(): RESTService
    {
        return new RESTService($this->shopDomain, $this->accessToken);
    }

    public function graph(): GraphService
    {
        return new GraphService($this->shopDomain, $this->accessToken);
    }
}
