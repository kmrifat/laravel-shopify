<?php

namespace Debutify\Shopify\Jobs;

use Debutify\Shopify\Adapters\ShopifyWebhookAdapter;
use Debutify\Shopify\Services\Rest\RESTService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WebHookRegisterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private string $shopDomain;

    /**
     * @var string
     */
    private string $accessToken;

    private RESTService $restService;


    /**
     * Create a new job instance.
     */
    public function __construct(string $shopDomain, string $accessToken)
    {
        $this->shopDomain = $shopDomain;
        $this->accessToken = $accessToken;
        $this->restService = new RESTService($this->shopDomain, $this->accessToken);

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $webhooks = (new ShopifyWebhookAdapter(config('shopify.webhooks')))->shopifyWebhookRequest();
        $webhooks->each(function ($webhook) {
            $this->restService->post('webhooks', [
                'webhook' => $webhook
            ]);
        });
    }
}
