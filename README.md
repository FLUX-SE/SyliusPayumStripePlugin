[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]

## Sylius Payum Stripe checkout session gateway plugin

This plugin is designed to add a new gateway to Payum to support Stripe checkout session over Sylius plugin

See https://stripe.com/docs/payments/checkout for more information.

## Installation

Install using Composer :

```
$ composer require prometee/sylius-payum-stripe-checkout-session-plugin
```

Enable this plugin :

```php
<?php

# config/bundles.php

return [
    // ...
    Prometee\SyliusPayumStripeCheckoutSessionPlugin\PrometeeSyliusPayumStripeCheckoutSessionPlugin::class => ['all' => true],
    // ...
];
```

## Configuration

### API keys

Get your `publishable_key` and your `secret_key` on your Stripe account :

https://dashboard.stripe.com/test/apikeys

### Webhook key
Then get a `webhook_secret_key` configured with at least two events : 
`payment_intent.canceled` and `checkout.session.completed`

https://dashboard.stripe.com/test/webhooks

### Sylius configuration

Go to the admin area, log in, then click on the left menu item "CONFIGURATION > Payment methods".
Create a new payment method type "Stripe Checkout Session (with SCA support)" :

![Create a new payment method][docs-assets-create-payment-method]

Then a form will be displayed, fill-in the required fields :

 1. the "code" field (ex: "stripe_session_checkout_with_sca").
 2. choose which channels this payment method will be affected to.
 3. the gateway configuration ([need info from here](#api-keys)) :
 
    ![Gateway Configuration][docs-assets-gateway-configuration]
    
    _NOTE1: You can add as many webhook secret keys as you need here, however generic usage need only one._
    
    _NOTE2: the screenshot contains false test credentials._
 4. give to this payment method a display name (and a description) for each languages you need
 
 Finally click on the "Create" button to save your new payment method.

## Advanced usages

See documentation here : https://github.com/Prometee/PayumStripeCheckoutSession/blob/master/README.md

[docs-assets-create-payment-method]: docs/assets/create-payment-method.png
[docs-assets-gateway-configuration]: docs/assets/gateway-configuration.png

[ico-version]: https://img.shields.io/packagist/v/Prometee/sylius-payum-stripe-checkout-session-plugin.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Prometee/SyliusPayumStripeCheckoutSessionPlugin/master.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Prometee/SyliusPayumStripeCheckoutSessionPlugin.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/prometee/sylius-payum-stripe-checkout-session-plugin
[link-travis]: https://travis-ci.org/Prometee/PayumStripeCheckoutSessionPlugin
[link-scrutinizer]: https://scrutinizer-ci.com/g/Prometee/SyliusPayumStripeCheckoutSessionPlugin/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/Prometee/SyliusPayumStripeCheckoutSessionPlugin
