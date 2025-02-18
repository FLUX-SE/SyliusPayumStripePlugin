<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class StripeGatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('publishable_key', TextType::class, [
                'label' => 'flux_se_sylius_payum_stripe_plugin.form.gateway_configuration.stripe.publishable_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'flux_se_sylius_payum_stripe_plugin.stripe.publishable_key.not_blank',
                        'groups' => [
                            'sylius',
                            'stripe_checkout_session',
                            'stripe_js',
                        ],
                    ]),
                ],
            ])
            ->add('secret_key', TextType::class, [
                'label' => 'flux_se_sylius_payum_stripe_plugin.form.gateway_configuration.stripe.secret_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'flux_se_sylius_payum_stripe_plugin.stripe.secret_key.not_blank',
                        'groups' => [
                            'sylius',
                            'stripe_checkout_session',
                            'stripe_js',
                        ],
                    ]),
                ],
            ])
            ->add('use_authorize', CheckboxType::class, [
                'required' => false,
                'label' => 'flux_se_sylius_payum_stripe_plugin.form.gateway_configuration.stripe.use_authorize',
            ])
            ->add('webhook_secret_keys', LiveCollectionType::class, [
                'label' => 'flux_se_sylius_payum_stripe_plugin.form.gateway_configuration.stripe.webhook_secret_keys',
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'button_delete_options' => [
                    'label' => 'sylius.ui.delete',
                    'translation_domain' => 'messages',
                    'attr' => [
                        'class' => 'btn btn-danger',
                    ],
                ],
                'button_add_options' => [
                    'label' => 'sylius.ui.add',
                ],
                'error_bubbling' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'flux_se_sylius_payum_stripe_plugin.stripe.webhook_secret_keys.not_blank',
                        'groups' => [
                            'sylius',
                            'stripe_checkout_session',
                            'stripe_js',
                        ],
                    ]),
                ],
                'entry_options' => [
                    'label' => false,
                    'translation_domain' => false,
                    'attr' => [
                        'placeholder' => 'whsec_',
                    ],
                ],
            ])
        ;
    }
}
