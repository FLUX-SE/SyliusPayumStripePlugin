<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Form\Type;

use Sylius\Bundle\PayumBundle\Form\Type\StripeGatewayConfigurationType as BaseStripeGatewayConfigurationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class StripeCheckoutSessionGatewayConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('webhook_secret_keys', CollectionType::class, [
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'label' => 'prometee_stripe_checkout_session_plugin.form.gateway_configuration.stripe.webhook_secret_keys',
                'constraints' => [
                    new NotBlank([
                        'message' => 'prometee_stripe_checkout_session_plugin.stripe.webhook_secret_keys.not_blank',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return BaseStripeGatewayConfigurationType::class;
    }
}
