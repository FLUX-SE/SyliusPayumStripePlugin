# UPGRADE FROM `v2.0.8` to `v2.0.9`

This class has been renamed :

- `\FluxSE\SyliusPayumStripePlugin\StateMachine\CompleteAuthorizedOrderProcessor`
  to `\FluxSE\SyliusPayumStripePlugin\StateMachine\CaptureAuthorizedOrderProcessor`

This service has been renamed :

- `flux_se.sylius_payum_stripe.state_machine.complete_authorized`
  to `flux_se.sylius_payum_stripe.state_machine.capture_authorized`

# UPGRADE FROM `v2.0.7` to `v2.0.8`

This class has been renamed :

- `\FluxSE\SyliusPayumStripePlugin\StateMachine\CancelAuthorizedOrderProcessor`
  to `\FluxSE\SyliusPayumStripePlugin\StateMachine\CancelOrderProcessor`

This service has been renamed :

- `flux_se.sylius_payum_stripe.state_machine.cancel_authorized`
  to `flux_se.sylius_payum_stripe.state_machine.cancel`

# UPGRADE FROM `v1.2` TO `v2.0.0`

You will have to create or edit the configuration file :

```yaml
# config/packages/flux_se_sylius_payum_stripe.yaml

# add this imported file
imports:
    - { resource: "@FluxSESyliusPayumStripePlugin/Resources/config/config.yaml" }

flux_se_sylius_payum_stripe:
#  refund_disabled: true # set to false to enable refund
# ... keep the existing config
```

# UPGRADE FROM `v1.1.2` TO `v1.2.0`

* **BC BREAK**: This Sylius plugin has been renamed from
 `SyliusPayumStripeCheckoutSessionPlugin` to `SyliusPayumStripePlugin`
 to handle more than one gateway from Stripe.
* **BC BREAK**: Rename the namespace (vendor and plugin name) from 
 `Prometee\SyliusPayumStripeCheckoutSessionPlugin` to `FluxSE\SyliusPayumStripePlugin`
* **BC BREAK**: Rename the config root name from 
 `prometee_sylius_payum_stripe_session_checkout` to `flux_se_sylius_payum_stripe
* **BC BREAK**: Rename the parameters from 
 `prometee_sylius_payum_stripe_checkout_session.*` to `flux_se_sylius_payum_stripe.*`
* **BC BREAK**: Rename the service names from 
 `prometee_sylius_payum_stripe_checkout_session.*` to `flux_se.sylius_payum_stripe.*` 
 `prometee.sylius_payum_stripe_checkout_session.*` to `flux_se.sylius_payum_stripe.*`
* **BC BREAK**: Rename translation root name from
 `prometee_stripe_checkout_session_plugin` to `flux_se_sylius_payum_stripe`
