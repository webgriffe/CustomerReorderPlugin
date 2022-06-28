<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /** @psalm-suppress UnusedVariable */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_customer_reorder_plugin');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
