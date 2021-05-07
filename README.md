[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Quality Score][ico-code-quality]][link-code-quality]

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

Then create a new endpoint with at least two events :
 
 - `payment_intent.canceled`
 - `checkout.session.completed`
 - `payment_intent.succeeded` (⚠️ Only when using Authorize flow)


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

Then start to listen for the 2 required events, forwarding request to your local server :

```shell
stripe listen \
    --events checkout.session.completed,payment_intent.canceled \
    --forward-to https://localhost/payment/notify/unsafe/stripe_checkout_session_with_sca
```

> 💡 Replace the --forward-to argument value with the right one you need.

When the command finishes a webhook secret key is displayed, copy it to your Payment method
in the Sylius admin.

> ⚠️ Using `stripe trigger checkout.session.completed` will always result in a `500 error`,
> because the test object will not embed any usable metadata.

## Advanced usages

See documentation here : https://github.com/FLUX-SE/PayumStripe/blob/master/README.md

[docs-assets-create-payment-method]: docs/assets/create-payment-method.png
[docs-assets-gateway-configuration]: docs/assets/gateway-configuration.png
[docs-assets-gateway-configuration-authorize]: docs/assets/gateway-configuration-authorize.png

[ico-version]: https://img.shields.io/packagist/v/Flux-SE/sylius-payum-stripe-plugin.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-github-actions]: https://github.com/FLUX-SE/SyliusPayumStripePlugin/workflows/Build/badge.svg
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Flux-SE/SyliusPayumStripePlugin.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/flux-se/sylius-payum-stripe-plugin
[link-scrutinizer]: https://scrutinizer-ci.com/g/FLUX-SE/SyliusPayumStripePlugin/code-structure
[link-github-actions]: https://github.com/FLUX-SE/SyliusPayumStripePlugin/actions?query=workflow%3A"Build"
[link-code-quality]: https://scrutinizer-ci.com/g/FLUX-SE/SyliusPayumStripePlugin
