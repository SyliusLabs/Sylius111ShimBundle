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
use SyliusLabs\Sylius111ShimBundle\DependencyInjection\CompilerPass\BehatEmailSpoolHookCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class BehatEmailSpoolHookCompilerPassTest extends TestCase
{
    public function testAddAliasForLegacyBehatEmailSpoolHook(): void
    {
        $container = new ContainerBuilder();
        $container->set('sylius.behat.context.hook.mailer', new \stdClass());

        $this->process($container);

        self::assertTrue($container->has('sylius.behat.context.hook.email_spool'));
    }

    private function process(ContainerBuilder $container): void
    {
        $compilerPass = new BehatEmailSpoolHookCompilerPass();
        $compilerPass->process($container);
    }
}
