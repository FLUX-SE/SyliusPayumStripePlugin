<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class StripeCheckoutSessionGatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('publishable_key', TextType::class, [
                'label' => 'flux_se_sylius_payum_stripe_plugin.form.gateway_configuration.stripe.publishable_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'flux_se_sylius_payum_stripe_plugin.stripe.publishable_key',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->add('secret_key', TextType::class, [
                'label' => 'flux_se_sylius_payum_stripe_plugin.form.gateway_configuration.stripe.secret_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'flux_se_sylius_payum_stripe_plugin.stripe.secret_key',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->add('use_authorize', CheckboxType::class, [
                'label' => 'flux_se_sylius_payum_stripe_plugin.form.gateway_configuration.stripe.use_authorize',
            ])
            ->add('webhook_secret_keys', CollectionType::class, [
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'label' => 'flux_se_sylius_payum_stripe_plugin.form.gateway_configuration.stripe.webhook_secret_keys',
                'constraints' => [
                    new NotBlank([
                        'message' => 'flux_se_sylius_payum_stripe_plugin.stripe.webhook_secret_keys.not_blank',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
        ;
    }
}
