[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Quality Score][ico-code-quality]][link-code-quality]

## Sylius Payum Stripe checkout session gateway plugin

This plugin is designed to add a new gateway to Payum to support Stripe checkout session over Sylius plugin

See https://stripe.com/docs/payments/checkout for more information.

## Installation

Install using Composer :

```
$ composer require flux-se/sylius-payum-stripe-plugin
```

Enable this plugin :

```php
<?php

# config/bundles.php

return [
    // ...
    FluxSE\SyliusPayumStripePlugin\FluxSESyliusPayumStripePlugin::class => ['all' => true],
    // ...
];
```

## Configuration

### API keys

Get your `publishable_key` and your `secret_key` on your Stripe account :

https://dashboard.stripe.com/test/apikeys

### Webhook key
Then get a `webhook_secret_key` configured with at least two events :
 
 - `payment_intent.canceled`
 - `checkout.session.completed`

The URL to fill is the route named `payum_notify_do_unsafe`, here is an example :

```
http://localhost/payment/notify/unsafe/stripe_session_checkout_with_sca
```

https://dashboard.stripe.com/test/webhooks

**/!\ Warning. Testing the webhooks with Stripe test webhook from the interface will always result in a 500 error such as the following one even if the webhook is correctly configured.**

![image](https://user-images.githubusercontent.com/9363039/109535376-c99ecd00-7abc-11eb-9b26-9b634acc83ca.png)

### Test or dev environment

Webhooks are triggered by Stripe on their server to your server.
If the server is into a private network, Stripe won't be allowed to reach your server.

Stripe provide an alternate way to catch those webhook events, you can use
`Stripe cli` : https://stripe.com/docs/stripe-cli
Follow the link and install `Stripe cli`, then use those command line to get
your webhook key :

First login to your Stripe account (needed every 90 days) :

```bash
stripe login
```

Then start to listen for the 2 required events, forwarding request to you local server :

```bash
stripe listen \
    --events checkout.session.completed,payment_intent.canceled \
    --forward-to https://localhost/payment/notify/unsafe/stripe_session_checkout_with_sca
```

> Replace the --forward-to argument value with the right one you need.

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
 4. give to this payment method a display name (and a description) for each language you need
 
 Finally, click on the "Create" button to save your new payment method.

## Advanced usages

See documentation here : https://github.com/FLUX-SE/PayumStripe/blob/master/README.md

[docs-assets-create-payment-method]: docs/assets/create-payment-method.png
[docs-assets-gateway-configuration]: docs/assets/gateway-configuration.png

[ico-version]: https://img.shields.io/packagist/v/Flux-SE/sylius-payum-stripe-plugin.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-github-actions]: https://github.com/FLUX-SE/SyliusPayumStripePlugin/workflows/Build/badge.svg
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Flux-SE/SyliusPayumStripePlugin.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/flux-se/sylius-payum-stripe-plugin
[link-scrutinizer]: https://scrutinizer-ci.com/g/FLUX-SE/SyliusPayumStripePlugin/code-structure
[link-github-actions]: https://github.com/FLUX-SE/SyliusPayumStripePlugin/actions?query=workflow%3A"Build"
[link-code-quality]: https://scrutinizer-ci.com/g/FLUX-SE/SyliusPayumStripePlugin
