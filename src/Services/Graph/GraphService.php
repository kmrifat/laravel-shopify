<?php

namespace Debutify\Shopify\Services\Graph;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GraphService
{
    /**
     * @var string
     */
    protected string $shop_name;

    /**
     * @var string
     */
    protected string $access_token;

    /**
     * @var mixed|Repository|Application|\Illuminate\Foundation\Application
     */
    protected mixed $api_version;

    /**
     * @var RequestStack
     */
    protected RequestStack $requestStack;

    public function __construct($shop_name, $access_token)
    {
        $this->shop_name = $shop_name;
        $this->access_token = $access_token;
        $this->api_version = config('shopify.api_version');
        $this->requestStack = new RequestStack();
    }

    /**
     *
     * Make Request is a super method which is return HTTP Facade
     * Rather throwing an error on 429 it's sleep the request for 500ms and retry
     * @return PendingRequest
     */
    private function makeRequest(): PendingRequest
    {
        return Http::withHeaders([
            'X-Shopify-Access-Token' => $this->access_token
        ])->withOptions([
            'verify' => false,
//            'handler' => $this->requestStack->retryStack()
        ]);
    }

    public function executeQuery(array $graphQlQuery): PromiseInterface|Response
    {
        return $this->makeRequest()->post($this->getUrl(), $graphQlQuery);
    }

    /**
     *
     * @param string $uri
     * @param array|null $query
     * @return string
     */
    private function getUrl(): string
    {
        return "https://{$this->shop_name}/admin/api/{$this->api_version}/graphql.json";
    }
}
