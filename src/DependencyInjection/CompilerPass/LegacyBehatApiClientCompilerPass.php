<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SyliusLabs\Sylius111ShimBundle\DependencyInjection\CompilerPass;

use SyliusLabs\Sylius111ShimBundle\Behat\ApiClient\LegacyApiPlatformClient;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class LegacyBehatApiClientCompilerPass implements CompilerPassInterface
{
    public const LEGACY_BEHAT_API_CLIENT_RESOURCES = [
        ['address', 'addresses', 'shop'],
        ['shipping_methods', 'shipping-methods', 'shop'],
        ['administrator', 'administrators', 'admin'],
        ['avatar_image', 'avatar-images', 'admin'],
        ['channel', 'channels', 'admin'],
        ['channel', 'channels', 'shop'],
        ['payment_method', 'payment-methods', 'shop'],
        ['currency', 'currencies', 'shop'],
        ['country', 'countries', 'admin'],
        ['country', 'countries', 'shop'],
        ['currency', 'currencies', 'admin'],
        ['customer_group', 'customer-groups', 'admin'],
        ['exchange_rate', 'exchange-rates', 'admin'],
        ['locale', 'locales', 'admin'],
        ['payment', 'payments', 'admin'],
        ['product_association_type', 'product-association-types', 'admin'],
        ['product_option', 'product-options', 'admin'],
        ['product_review', 'product-reviews', 'admin'],
        ['product', 'products', 'admin'],
        ['product_variant', 'product-variants', 'admin'],
        ['product', 'products', 'shop'],
        ['product_variant', 'product-variants', 'shop'],
        ['product_review', 'product-reviews', 'shop'],
        ['province', 'provinces', 'admin'],
        ['order', 'orders', 'shop'],
        ['shipping_category', 'shipping-categories', 'admin'],
        ['shipping_method', 'shipping-methods', 'admin'],
        ['tax_category', 'tax-categories', 'admin'],
        ['shipment', 'shipments', 'admin'],
        ['order', 'orders', 'admin'],
        ['order', 'orders', 'shop'],
        ['order_item', 'order-items', 'shop'],
        ['order_item_unit', 'order-item-units', 'shop'],
        ['payment', 'payments', 'shop'],
        ['shipment', 'shipments', 'shop'],
        ['taxon', 'taxons', 'admin'],
        ['zone', 'zones', 'admin'],
        ['promotion', 'promotions', 'admin'],
        ['customer', 'customers', 'shop'],
        ['locale', 'locales', 'shop'],
        ['catalog_promotion', 'catalog-promotions', 'admin'],
        ['catalog_promotion', 'catalog-promotions', 'shop'],
    ];

    public function process(ContainerBuilder $container): void
    {
        foreach (self::LEGACY_BEHAT_API_CLIENT_RESOURCES as [$resourceSingular, $resourcePlural, $section]) {
            $serviceId = sprintf('sylius.behat.api_platform_client.%s.%s', $section, $resourceSingular);

            if ($container->hasDefinition($serviceId)) {
                continue;
            }

            $apiClientDefinition = new Definition(LegacyApiPlatformClient::class);
            $apiClientDefinition
                ->setArguments([
                    new Reference('test.client'),
                    new Reference('sylius.behat.shared_storage'),
                    '%sylius.api.authorization_header%',
                    $section,
                    $resourcePlural,
                ])
                ->setPublic(true)
            ;
            $container->setDefinition($serviceId, $apiClientDefinition);
        }

        if ($container->hasDefinition('sylius.behat.api_platform_client.admin.administrator') && !$container->hasDefinition('sylius.behat.api_platform_client.administrator')) {
            $container->setAlias('sylius.behat.api_platform_client.administrator', 'sylius.behat.api_platform_client.admin.administrator');
        }

        $abstractApiPlatformClientDefinition = new Definition(LegacyApiPlatformClient::class);
        $abstractApiPlatformClientDefinition
            ->setArguments([
                new Reference('test.client'),
                new Reference('sylius.behat.shared_storage'),
                '%sylius.api.authorization_header%',
            ])
            ->setAbstract(true)
            ->setPublic(true)
        ;

        $container->setDefinition('sylius.behat.api_platform_client.legacy', $abstractApiPlatformClientDefinition);
    }
}
