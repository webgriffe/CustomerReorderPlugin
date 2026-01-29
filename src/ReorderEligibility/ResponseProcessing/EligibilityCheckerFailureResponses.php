<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing;

final class EligibilityCheckerFailureResponses
{
    public const string INSUFFICIENT_ITEM_QUANTITY = 'sylius.reorder.insufficient_quantity';

    public const string ITEMS_OUT_OF_STOCK = 'sylius.reorder.items_out_of_stock';

    public const string REORDER_ITEMS_PRICES_CHANGED = 'sylius.reorder.items_price_changed';

    public const string REORDER_PROMOTIONS_CHANGED = 'sylius.reorder.promotion_not_enabled';

    public const string TOTAL_AMOUNT_CHANGED = 'sylius.reorder.previous_order_total';
}
