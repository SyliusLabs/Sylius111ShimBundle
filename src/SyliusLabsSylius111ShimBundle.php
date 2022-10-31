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

namespace SyliusLabs\Sylius111ShimBundle;

use SyliusLabs\Sylius111ShimBundle\DependencyInjection\CompilerPass\LegacyBehatApiClientCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyliusLabsSylius111ShimBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new LegacyBehatApiClientCompilerPass());
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return null;
    }
}
