<?php
declare(strict_types=1);

namespace App\Providers;

final class ProviderRegistry
{
    private array $exchangeRateProviders = [];

    public function __construct(iterable $exchangeRateProviders)
    {
        /** @var ExchangeRateProvider $exchangeRateProvider */
        foreach ($exchangeRateProviders as $exchangeRateProvider) {
            $this->exchangeRateProviders[$exchangeRateProvider->getPriority()] = $exchangeRateProvider;
        }
        ksort($this->exchangeRateProviders);
    }

    public function getAll(): array
    {
        return $this->exchangeRateProviders;
    }
}
