<?php

declare(strict_types=1);

namespace Tests\Sylius\CustomerReorderPlugin\Behat\Page\Cart;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Shop\Cart\SummaryPage as BaseSummaryPage;

final class SummaryPage extends BaseSummaryPage implements SummaryPageInterface
{
    public function checkout(): void
    {
        $this->getSession()->getPage()->clickLink('Checkout');
    }

    public function countFlashMessages(): int
    {
        return count($this->getSession()->getPage()->findAll('css', '[data-test-sylius-flash-message]'));
    }

    public function doesFlashMessageWithTextExists(string $text): bool
    {
        $notifications = $this->getSession()->getPage()->findAll('css', '[data-test-sylius-flash-message]');

        if (0 === count($notifications)) {
            return false;
        }

        /** @var NodeElement $notification */
        foreach ($notifications as $notification) {
            $message = $notification->getText();

            $strpos = strpos($message, $text);
            if ($strpos !== false) {
                return true;
            }
        }

        return false;
    }
}
