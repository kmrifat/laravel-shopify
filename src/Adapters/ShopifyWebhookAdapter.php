<?php

namespace Kmrifat\Shopify\Adapters;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ShopifyWebhookAdapter
{

    /**
     * @var array|string[][]
     */
    protected array $webhookArray;

    /**
     * @var string
     */
    private string $webhookBaseUrl;

    /**
     * @param array<array<string, string>> $data
     */
    public function __construct(array $data)
    {
        $this->webhookArray = $data;
        $this->setWebhookBaseUrl(config('shopify.webhook_url'));
    }

    /**
     * @return Collection
     */
    public function webhookCollection(): Collection
    {
        return collect($this->webhookArray)->map(fn($item) => (object)[
            'method' => 'POST',
            'uri' => "{$this->webhookBaseUrl}debutify-shopify/webhook/" . Str::replace('/', '-', $item[0]),
            'uri_topic' => Str::replace('/', '-', $item[0]),
            'controller' => 'Debutify\Shopify\Http\Controllers\WebhookController@index',
            'topic' => $item[0],
            'job' => $item[1]
        ]);
    }

    /**
     * @return Collection
     */
    public function shopifyWebhookRequest()
    {
        return $this->webhookCollection()->map(fn($item) => [
            'topic' => $item->topic,
            'address' => $item->uri,
            'format' => 'json',
        ]);
    }

    /**
     * @param string $webhookBaseUrl
     */
    public function setWebhookBaseUrl(string $webhookBaseUrl): void
    {
        if (!Str::endsWith($webhookBaseUrl, '/')) {
            $webhookBaseUrl .= '/';
        }
        $this->webhookBaseUrl = $webhookBaseUrl;
    }
}
