[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]

## Sylius Payum Stripe gateway plugin

This plugin is designed to add a new gateway to Payum to support Stripe Checkout Session.
It supports [one time payment](https://stripe.com/docs/payments/accept-a-payment?integration=checkout)
and authorized payment by [placing a hold on a card](https://stripe.com/docs/payments/capture-later).

Refund is also possible but disabled by default to avoid mistakes, use this config to enable it :
```yaml
# config/packages/flux_se_sylius_payum_stripe.yaml

flux_se_sylius_payum_stripe:
  refund_disabled: false
```

See https://stripe.com/docs/payments/checkout for more information.

## Installation

Install using Composer :

```shell
composer remove --dev stripe/stripe-php
composer require flux-se/sylius-payum-stripe-plugin
```

> 💡 If the flex recipe has not been applied then follow the next step.

Enable this plugin :

```php
<?php

# config/bundles.php

return [
    // ...
    FluxSE\SyliusPayumStripePlugin\FluxSESyliusPayumStripePlugin::class => ['all' => true],    
    FluxSE\PayumStripeBundle\FluxSEPayumStripeBundle::class => ['all' => true],
    // ...
];
```

Create the file `config/packages/flux_se_sylius_payum_stripe.yaml` and add the following content

```yaml
imports:
  - { resource: "@FluxSESyliusPayumStripePlugin/Resources/config/config.yaml" }
```

## Configuration

### Sylius configuration

Go to the admin area, log in, then click on the left menu item "CONFIGURATION > Payment methods".
Create a new payment method type "Stripe Checkout Session (with SCA support)" :

![Create a new payment method][docs-assets-create-payment-method]

Then a form will be displayed, fill-in the required fields :

#### 1. the "code" field (ex: "stripe_checkout_session_with_sca").

> 💡 The code will be the `gateway name`, it will be needed to build the right webhook URL later
> (see [Webhook key](#webhook-key) section for more info).

#### 2. choose which channels this payment method will be affected to.

#### 3. the gateway configuration ([need info from here](#api-keys)) :

   ![Gateway Configuration][docs-assets-gateway-configuration]

   ![Gateway Configuration][docs-assets-gateway-configuration-authorize]

   > _📖 NOTE1: You can add as many webhook secret keys as you need here, however generic usage need only one._

   > _📖 NOTE2: the screenshot contains false test credentials._

#### 4. give to this payment method a display name (and a description) for each language you need.

Finally, click on the "Create" button to save your new payment method.

### API keys

Get your `publishable_key` and your `secret_key` on your Stripe dashboard :

https://dashboard.stripe.com/test/apikeys

### Webhook key

Got to :

https://dashboard.stripe.com/test/webhooks

Then create a new endpoint with those events:

 | Gateway | `stripe_checkout_session` | `stripe_js` |
|-|-|-|
| Webhook events |  - `checkout.session.completed`<br> - `checkout.session.async_payment_failed`<br> - `checkout.session.async_payment_succeeded`<br> - `setup_intent.canceled` (⚠️ Only when using `setup` mode)<br> - `setup_intent.succeeded`  (⚠️ Only when using `setup` mode) |  - `payment_intent.canceled`<br> - `payment_intent.succeeded`<br> - `setup_intent.canceled` (⚠️ Only when using `setup` mode)<br> - `setup_intent.succeeded`  (⚠️ Only when using `setup` mode) |


The URL to fill is the route named `payum_notify_do_unsafe` with the `gateway`
param equal to the `gateway name` (Payment method code), here is an example :

```
https://localhost/payment/notify/unsafe/stripe_checkout_session_with_sca
```

> 📖 As you can see in this example the URL is dedicated to `localhost`, you will need to provide to
> Stripe a public host name in order to get the webhooks working.

> 📖 Use this command to know the exact structure of `payum_notify_do_unsafe` route
> 
> ```shell
> bin/console debug:router payum_notify_do_unsafe
> ```

> 📖 Use this command to know the exact name of your gateway,
> or just check the `code` of the payment method in the Sylius admin payment method index.
> 
> ```shell
> bin/console debug:payum:gateway
> ```

### Test or dev environment

Webhooks are triggered by Stripe on their server to your server.
If the server is into a private network, Stripe won't be allowed to reach your server.

Stripe provide an alternate way to catch those webhook events, you can use
`Stripe cli` : https://stripe.com/docs/stripe-cli
Follow the link and install `Stripe cli`, then use those command line to get
your webhook key :

First login to your Stripe account (needed every 90 days) :

```shell
stripe login
```

Then start to listen for the Stripe events (minimal ones are used here), forwarding request to your local server :

1. Example with `stripe_checkout_session_with_sca` as gateway name:
   ```shell
   stripe listen \
      --events checkout.session.completed,checkout.session.async_payment_failed,checkout.session.async_payment_succeeded \
      --forward-to https://localhost/payment/notify/unsafe/stripe_checkout_session_with_sca
   ```
1. Example with `stripe_js_with_sca` as gateway name:
   ```shell
   stripe listen \
      --events payment_intent.canceled,payment_intent.succeeded \
      --forward-to https://localhost/payment/notify/unsafe/stripe_js_with_sca
   ```

> 💡 Replace the --forward-to argument value with the right one you need.

When the command finishes a webhook secret key is displayed, copy it to your Payment method
in the Sylius admin.

> ⚠️ Using the command `stripe trigger checkout.session.completed` will always result in a `500 error`,
> because the test object will not embed any usable metadata.

### More?

See documentation here : https://github.com/FLUX-SE/PayumStripe/blob/master/README.md

## API (Sylius Api Platform)

### Stripe JS gateway

The endpoint : `GET /api/v2/shop/orders/{tokenValue}/payments/{paymentId}/configuration`
will make a Payum `Capture` or an `Authorize` and respond with the Stripe Payment Intent client secret, like this :

```json
{
 'publishable_key':  'pk_test_1234567890',
 'use_authorize': false,
 'stripe_payment_intent_client_secret': 'a_secret'
}
```

After calling this endpoint your will be able to use Stripe Elements to display a Stripe Payment form, the same as this template is doing it:
https://github.com/FLUX-SE/PayumStripe/blob/master/src/Resources/views/Action/stripeJsPaymentIntent.html.twig.
More information here : https://docs.stripe.com/payments/payment-element

### Stripe Checkout Session gateway

The endpoint : `GET /api/v2/shop/orders/{tokenValue}/payments/{paymentId}/configuration`
will make a Payum `Capture` or an `Authorize` and respond with the Stripe Checkout Session url, like this :

```json
{
 'publishable_key':  'pk_test_1234567890',
 'use_authorize': false,
 'stripe_checkout_session_url': 'https://checkout.stripe.com/c/pay/cs_test...'
}
```

Since this endpoint is not able to get any data from you, a service can be decorated to specify the Stripe Checkout Session `success_url` you need. 
Decorate this service : `flux_se.sylius_payum_stripe.api.payum.after_url.stripe_checkout_session` to generate your own dedicated url.
You will have access to the Sylius `Payment` to decide what is the url/route and the parameters of it.

[docs-assets-create-payment-method]: docs/assets/create-payment-method.png
[docs-assets-gateway-configuration]: docs/assets/gateway-configuration.png
[docs-assets-gateway-configuration-authorize]: docs/assets/gateway-configuration-authorize.png

[ico-version]: https://img.shields.io/packagist/v/Flux-SE/sylius-payum-stripe-plugin.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-github-actions]: https://github.com/FLUX-SE/SyliusPayumStripePlugin/workflows/Build/badge.svg

[link-packagist]: https://packagist.org/packages/flux-se/sylius-payum-stripe-plugin
[link-scrutinizer]: https://scrutinizer-ci.com/g/FLUX-SE/SyliusPayumStripePlugin/code-structure
[link-github-actions]: https://github.com/FLUX-SE/SyliusPayumStripePlugin/actions?query=workflow%3A"Build"
