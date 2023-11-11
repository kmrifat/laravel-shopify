<?php

namespace Kmrifat\Shopify\Socialites;


use Kmrifat\Shopify\Jobs\WebHookRegisterJob;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Str;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class SocialiteShopifyProvider extends AbstractProvider implements ProviderInterface
{

    protected string $token = '';

    /**
     * @inheritDoc
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->shopifyUrl("/admin/oauth/authorize"), $state);
    }

    /**
     * @inheritDoc
     */
    protected function getTokenUrl()
    {
        return $this->shopifyUrl('/admin/oauth/access_token');
    }

    /**
     * @inheritDoc
     */
    protected function getUserByToken($token)
    {
        $this->token = $token;

        $response = $this->getHttpClient()->get($this->shopifyUrl('/admin/shop.json'), [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'X-Shopify-Access-Token' => $this->token,
            ],
        ]);

        return json_decode((string)$response->getBody(), true)['shop'];
    }

    /**
     * Dispatch Webhook Register Job on login.
     * @inheritDoc
     */
    protected function mapUserToObject(array $user)
    {
        dispatch(new WebHookRegisterJob($user['myshopify_domain'], $this->token));
        return (new User())->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => $user['myshopify_domain'],
            'name' => $user['name'],
            'email' => $user['email'],
            'avatar' => null,
            'token' => $this->token
        ]);
    }

    /**
     * @param $code
     * @return array|string[]
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function additionalConfigKeys()
    {
        return ['subdomain'];
    }

    private function shopifyUrl($uri = null)
    {
        if (!empty($this->parameters['subdomain'])) {
            return "https://{$this->parameters['subdomain']}.myshopify.com";
        }

        if ($this->request->has('shop')) {
            if (!Str::endsWith($this->request->shop, '.myshopify.com')) {
                return "https://{$this->request->shop}.myshopify.com{$uri}";
            }
        }

        return "https://{$this->request->get('shop')}$uri";
    }
}
