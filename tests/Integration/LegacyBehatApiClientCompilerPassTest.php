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

namespace SyliusLabs\Sylius111ShimBundle\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Sylius\Behat\Service\SharedStorage;
use SyliusLabs\Sylius111ShimBundle\Behat\ApiClient\LegacyApiPlatformClient;
use SyliusLabs\Sylius111ShimBundle\DependencyInjection\CompilerPass\LegacyBehatApiClientCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\HttpKernelBrowser;

final class LegacyBehatApiClientCompilerPassTest extends TestCase
{
    /**
     * @dataProvider legacyApiPlatformClientServiceIdsProvider
     */
    public function testAddLegacyApiPlatformClient(string $apiPlatformClientServiceId): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('sylius.api.authorization_header', 'RandomHeader');
        $container->set('test.client', $this->createMock(HttpKernelBrowser::class));
        $container->set('sylius.behat.shared_storage', $this->createMock(SharedStorage::class));

        $this->process($container);

        self::assertTrue($container->hasDefinition($apiPlatformClientServiceId));
        self::assertInstanceOf(LegacyApiPlatformClient::class, $container->get($apiPlatformClientServiceId));
    }

    public function testAddAdministratorAlias(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('sylius.api.authorization_header', 'RandomHeader');
        $container->set('test.client', $this->createMock(HttpKernelBrowser::class));
        $container->set('sylius.behat.shared_storage', $this->createMock(SharedStorage::class));

        $this->process($container);

        self::assertTrue($container->hasAlias('sylius.behat.api_platform_client.administrator'));
        self::assertInstanceOf(LegacyApiPlatformClient::class, $container->get('sylius.behat.api_platform_client.administrator'));
    }

    public function legacyApiPlatformClientServiceIdsProvider(): iterable
    {
        foreach (LegacyBehatApiClientCompilerPass::LEGACY_BEHAT_API_CLIENT_RESOURCES as [$resource, $section]) {
            yield [sprintf('sylius.behat.api_platform_client.%s.%s', $section, $resource)];
        }
    }

    private function process(ContainerBuilder $container): void
    {
        $compilerPass = new LegacyBehatApiClientCompilerPass();
        $compilerPass->process($container);
    }
}
