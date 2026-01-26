<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\CustomerReorderPlugin\DependencyInjection\Compiler\RegisterEligibilityCheckersPass;
use Sylius\CustomerReorderPlugin\DependencyInjection\Compiler\RegisterReorderProcessorsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @psalm-api
 */
final class SyliusCustomerReorderPlugin extends Bundle
{
    use SyliusPluginTrait;

    #[\Override]
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterEligibilityCheckersPass());
        $container->addCompilerPass(new RegisterReorderProcessorsPass());
    }

    #[\Override]
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
