<?php

use Kmrifat\Shopify\Adapters\ShopifyWebhookAdapter;
use Illuminate\Support\Facades\Route;

$shopify_webhooks = (new ShopifyWebhookAdapter(config('shopify.webhooks')))->webhookCollection();

$shopify_webhooks->each(function ($route) {
    Route::{strtolower($route->method)}('webhook/{topic}', $route->controller);
});
