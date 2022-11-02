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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class BehatEmailSpoolHookCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->has('sylius.behat.context.hook.email_spool')) {
            return;
        }

        $container->setAlias('sylius.behat.context.hook.email_spool', 'sylius.behat.context.hook.mailer')->setPublic(true);
    }
}
