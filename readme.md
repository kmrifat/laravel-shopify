# Laravel Shopify

It's an api first approach laravel wrapper, comes with shopify authentication and easy access shopify api.

### History

After deprecate of [gnikyt/laravel-shopify](https://github.com/gnikyt/laravel-shopify/discussions/1276), i tried
Shopify's official php wrapper, but i found it highly focus on shopify so hard to implement other platform on it, and
hard to build api oriented SAP app for shopify app store.

I was looking for something where i have the freedom to architect my own application, so i decided to build one.
I use laravel socialite so all the laravel dev can easily use this wrapper package hassle-free.

### Installation

use this command bellow in your existing laravel project

```bash
composer require kmrifat/laravel-shopify
```

### Publish providers

```bash
php artisan vendor:publish --provider="Kmrifat\Shopify\ShopifyServiceProvider"
php artisan migrate
```

### Using shopify in your project

```php
use Kmrifat\Shopify\Facades\Shopify;

$shopify = Shopify::shop('domain.myshopify.com','access_token');

// use rest get request
$shopify->rest()->get('products/{product_id}');
// you will only need to pass topic instate passing the entire url

// use post method to modify anything in shopify, the first argument will be the topic and second one will be the payload
$shopify->rest()->post('products', []);

// use graphql
$shopify->graph()->executeQuery([
    'query' => 'query_string',
    'variables' => [] // pass query variables
]);
```

For quick and easy access you can create a method in `User` model like this example bellow

```php
namespace App\Models;
use Kmrifat\Shopify\Facades\Shopify;

class User extends Authenticatable 
{
    public function shopify()
    {
        return return Shopify::shop($this->name, $this->shopify_access_token);
        // $this->name = shopify shop domain
        // $this->shopify_access_token = shopify store access token
        // you can store the shop domain and access token anywhere you want, all you just need to pass this in shop parameter
    }
}

```

### Env

add this keys and value in your env to power this package to perform authentication and other activities

```dotenv
SHOPIFY_APP_NAME= #put your app name and wrape them in duble quite "My Shopify APP"
SHOPIFY_APP_CLIENT_ID=#"Your shopify app client id"
SHOPIFY_APP_SECRET=#"Your shopify app secret"
SHOPIFY_APP_REDIRECT=#"Create a redirect url at your route and pase the redirect url here. i.e: /auth/callback"
SHOPIFY_APP_SCOPES=#put the app scopes and seperate them by (,) comma
SHOPIFY_WEBHOOK=#for local development you can put ngrok url to recieve the webhook response, for production replcae it by your main domain
```

If you are planning to build an api interactive app then you can ignore `SHOPIFY_APP_REDIRECT` as you are handling it by
the frontend. <br>
Checkout this link to [create shopify app](https://partners.shopify.com/)

### Authentication

The package using [socialite](https://laravel.com/docs/10.x/socialite) for shopify authentication, all you need to
use `shopify` driver in socialite. See the example bellow

```php
Route::get('/', function () {
    if (request()->has('shop')) {
    // If you want to authenticate in shopify store from the app, you just need to pass the shopify store name in shop query string,
    // i.e: yourdomain.com?shop=your-shopify-store 
        return \Laravel\Socialite\Facades\Socialite::driver('shopify')->stateless()->scopes(config('shopify.scopes'))
            ->with(['redirect_uri' => $request->redirect_uri ?? config('shopify.redirect')])
            ->redirect(); // use ->getTargetUrl() before closing statement if you are build an API oriented application, so it will return your the redirect url instate redirect from the backend
    }
    return view('welcome');
});

Route::get('/auth/callback', function () {
    $shopifyUser = \Laravel\Socialite\Facades\Socialite::driver('shopify')->stateless()->user();
    $user = User::updateOrCreate([
        'email' => $shopifyUser->email,
    ], [
        'name' => $shopifyUser->nickname,
        'email' => $shopifyUser->email,
        'shopify_access_token' => $shopifyUser->token,
    ]);
    
    // you can use passport / sanctum or any of favorite  to perform the authentication
    Auth::login($user, true);

    return redirect('/dashboard');
});
```

### Webhooks

To register webhook all you need is to create a job and register this job in `conifg/shopify.php`

```php
'webhooks' => [
    ['shopify/webhook', \App\Jobs\YourJob::class] // first element of this array will be the topic of webhook and second element of this array will your targeted job of the webhook
]
```

### Example Project
```bash

```