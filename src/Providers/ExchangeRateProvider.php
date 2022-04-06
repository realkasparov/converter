<?php
declare(strict_types=1);

namespace App\Providers;

interface ExchangeRateProvider
{
    /**
     * Import data from source
     */
    public function importRates(): array;

    /**
     * Base currency of provider
     */
    public function getSourceCurrency(): string;

    /**
     * Name of provider
     */
    public function getName(): string;

    /**
     * Priority - affects the order in which the data will be imported from sources
     */
    public function getPriority(): int;
}