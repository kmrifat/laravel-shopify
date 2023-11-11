<?php

namespace Kmrifat\Shopify\Services\Rest;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * REST Serivce for Shppify
 */
class RESTService
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

    /**
     * @param $shop_name
     * @param $access_token
     */
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
            'handler' => $this->requestStack->retryStack()
        ]);
    }

    /**
     * Retrieve Shopify Resources
     * @param string $uri
     * @param array|null $query
     * @return PromiseInterface|Response
     */
    public function get(string $uri, array $query = null): PromiseInterface|Response
    {
        return $this->makeRequest()->get($this->getUrl($uri, $query));
    }

    /**
     * Create/POST Shopify resource
     * @param string $uri
     * @param $data
     * @return Response|PromiseInterface
     */
    public function post(string $uri, $data): Response|PromiseInterface
    {
        return $this->makeRequest()->post($this->getUrl($uri), $data);
    }

    /**
     * Update Shopify resource
     * @param string $uri
     * @param $data
     * @return PromiseInterface|Response
     */
    public function put(string $uri, $data): PromiseInterface|Response
    {
        return $this->makeRequest()->put($this->getUrl($uri, []), $data);
    }

    /**
     * Delete Shopify resource
     * @param string $uri
     * @param $query
     * @return PromiseInterface|Response
     */
    public function delete(string $uri, $query): PromiseInterface|Response
    {
        return $this->makeRequest()->delete($this->getUrl($uri, $query));
    }

    /**
     * Generate Base URL by shop name and api version
     * @return string
     */
    private function getBaseUrl(): string
    {
        return "https://{$this->shop_name}/admin/api/{$this->api_version}";
    }

    /**
     *
     * @param string $uri
     * @param array|null $query
     * @return string
     */
    private function getUrl(string $uri, array $query = null): string
    {
        $query = http_build_query($query ?? []);
        return "{$this->getBaseUrl()}/{$uri}.json?{$query}";
    }
}
