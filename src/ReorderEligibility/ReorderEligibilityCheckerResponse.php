<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

class ReorderEligibilityCheckerResponse
{
    /** @param array<string, string> $parameters */
    public function __construct(
        private string $message,
        private array $parameters,
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    /** @param array<string, string> $parameters */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}
