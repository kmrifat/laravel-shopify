<?php

namespace Kmrifat\Shopify\Http\Controllers;

use App\Http\Controllers\Controller;
use Kmrifat\Shopify\Adapters\ShopifyWebhookAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Queue;

class WebhookController extends Controller
{
    /**
     * Get all webhook at this method and dispatch job
     * @param Request $request
     * @param string $topic Registered webhooks topic replace / by -
     * @return JsonResponse
     */
    public function index(Request $request, string $topic)
    {
        $shopifyWebhooks = (new ShopifyWebhookAdapter(config('shopify.webhooks')))->webhookCollection();
        $_topic = $shopifyWebhooks->where('uri_topic', $topic)->first();
        if ($_topic) {
            $jobClass = $_topic->job;
            $shopDomain = $request->header('X-Shopify-Shop-Domain');
            $jobData = json_decode($request->getContent());
            dispatch(new $jobClass($shopDomain, $jobData));
            return response()->json($shopifyWebhooks->where('uri_topic', $topic)->first());
        }
        return response()->json(["Invalid topic {$topic}. Allowed topics are", $shopifyWebhooks->pluck('uri_topic')], 404);

    }
}
