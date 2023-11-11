<?php

namespace Debutify\Shopify\Services\Rest;

use Closure;
use DateTime;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\RetryMiddleware;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RequestStack
{
    /**
     * @var int|Repository|Application|\Illuminate\Foundation\Application|mixed
     */
    private int $maxRetries;

    /**
     *
     */
    public function __construct()
    {
        $this->maxRetries = config('shopify.max_retries');
    }

    /**
     * @return HandlerStack
     */
    public function retryStack(): HandlerStack
    {
        $decider = $this->decider();
        $delay = $this->delay();

        $stack = HandlerStack::create();
        $stack->push(Middleware::retry($decider, $delay));

        return $stack;
    }

    /**
     * Decide retry on 429 until it's not reached at max retries
     * @return Closure
     */
    private function decider(): Closure
    {
        return function (int $retries, RequestInterface $request, ResponseInterface $response = null): bool {
            return
                $retries < $this->maxRetries
                && null !== $response
                && 429 === $response->getStatusCode();
        };
    }

    /**
     * Delay the HTTP Request for 1 second after getting 429 error
     * @return Closure
     */
    private function delay(): Closure
    {
        return function (int $retries, ResponseInterface $response): int {
            if (!$response->hasHeader('Retry-After')) {
                return RetryMiddleware::exponentialDelay($retries);
            }

            $retryAfter = $response->getHeaderLine('Retry-After');

            if (!is_numeric($retryAfter)) {
                $retryAfter = (new DateTime($retryAfter))->getTimestamp() - time();
            }

            return (int)$retryAfter * 1000;
        };
    }
}
