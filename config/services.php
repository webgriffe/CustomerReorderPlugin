<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\CustomerReorderPlugin\Checker\OrderCustomerRelationChecker;
use Sylius\CustomerReorderPlugin\Controller\CustomerReorderAction;
use Sylius\CustomerReorderPlugin\Factory\OrderFactory;
use Sylius\CustomerReorderPlugin\Reorder\Reorderer;
use Sylius\CustomerReorderPlugin\ReorderEligibility\CompositeReorderEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\InsufficientItemQuantityEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ItemsOutOfStockEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityConstraintMessageFormatter;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderItemPricesEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderPromotionsEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\ReorderEligibilityCheckerResponseProcessor;
use Sylius\CustomerReorderPlugin\ReorderEligibility\TotalReorderAmountEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderProcessing\CompositeReorderProcessor;
use Sylius\CustomerReorderPlugin\ReorderProcessing\ReorderDataProvider;
use Sylius\CustomerReorderPlugin\ReorderProcessing\ReorderItemsProcessor;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
    ;

    $services->set(CustomerReorderAction::class)
        ->args([
            service('sylius_shop.storage.cart_session'),
            service('sylius.context.channel'),
            service('sylius.context.customer'),
            service('sylius.repository.order'),
            service(Reorderer::class),
            service('router'),
            service('request_stack'),
        ])
    ;

    $services->set(Reorderer::class)
        ->args([
            service(OrderFactory::class),
            service('doctrine.orm.entity_manager'),
            service(CompositeReorderEligibilityChecker::class),
            service(ReorderEligibilityCheckerResponseProcessor::class),
            service(OrderCustomerRelationChecker::class),
        ])
    ;

    $services->set(OrderFactory::class)
        ->args([
            service('sylius.factory.order'),
            service(CompositeReorderProcessor::class),
        ])
    ;

    $services->set(CompositeReorderEligibilityChecker::class)
        ->public(false)
    ;

    $services->set(InsufficientItemQuantityEligibilityChecker::class)
        ->args([
            service(ReorderEligibilityConstraintMessageFormatter::class),
        ])
        ->tag('sylius_customer_reorder_plugin.eligibility_checker', ['priority' => 40])
    ;

    $services->set(ItemsOutOfStockEligibilityChecker::class)
        ->args([
            service(ReorderEligibilityConstraintMessageFormatter::class),
            service('sylius.checker.inventory.availability'),
        ])
        ->tag('sylius_customer_reorder_plugin.eligibility_checker', ['priority' => 30])
    ;

    $services->set(ReorderItemPricesEligibilityChecker::class)
        ->args([
            service(ReorderEligibilityConstraintMessageFormatter::class),
        ])
        ->tag('sylius_customer_reorder_plugin.eligibility_checker', ['priority' => 20])
    ;

    $services->set(ReorderPromotionsEligibilityChecker::class)
        ->args([
            service(ReorderEligibilityConstraintMessageFormatter::class),
        ])
        ->tag('sylius_customer_reorder_plugin.eligibility_checker', ['priority' => 10])
    ;

    $services->set('sylius.reorder.eligibility_checker.total', TotalReorderAmountEligibilityChecker::class)
        ->args([
            service('sylius.formatter.money'),
        ])
        ->tag('sylius_customer_reorder_plugin.eligibility_checker', ['priority' => 0])
    ;

    $services->set(CompositeReorderProcessor::class)
        ->public(false)
    ;

    $services->set(ReorderDataProvider::class)
        ->tag('sylius_customer_reorder_plugin.reorder_processor', ['priority' => 10])
    ;

    $services->set(ReorderItemsProcessor::class)
        ->args([
            service('sylius.modifier.order_item_quantity'),
            service('sylius.modifier.order'),
            service('sylius.checker.inventory.availability'),
            service('sylius.factory.order_item'),
        ])
        ->tag('sylius_customer_reorder_plugin.reorder_processor', ['priority' => 0])
    ;

    $services->set(ReorderEligibilityConstraintMessageFormatter::class);

    $services->set(ReorderEligibilityCheckerResponseProcessor::class)
        ->args([
            service('request_stack'),
        ])
    ;

    $services->set(OrderCustomerRelationChecker::class);
};
