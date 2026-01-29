<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing;

use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityCheckerResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

final class ReorderEligibilityCheckerResponseProcessor implements ReorderEligibilityCheckerResponseProcessorInterface
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    #[\Override]
    public function process(array $responses): void
    {
        /** @var ReorderEligibilityCheckerResponse $response */
        foreach ($responses as $response) {
            $session = $this->requestStack->getSession();
            if ($session instanceof Session) {
                $session->getFlashBag()->add('info', [
                    'message' => $response->getMessage(),
                    'parameters' => $response->getParameters(),
                ]);
            }
        }
    }
}
