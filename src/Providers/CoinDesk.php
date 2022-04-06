<?php
declare(strict_types=1);

namespace App\Providers;

use App\Helper\StringUtils;

final class CoinDesk extends HttpService implements ExchangeRateProvider
{
    private const SOURCE_URL = 'https://api.coindesk.com/v1/bpi/historical/close.json';

    private const SOURCE_CURRENCY = 'USD';

    /**
     * {@inheritDoc}
     */
    public function importRates(): array
    {
        $json = $this->request(self::SOURCE_URL);

        $data = StringUtils::jsonToArray($json);

        $date = new \DateTime('yesterday');
        if (isset($data['bpi'][$date->format('Y-m-d')])) {
            return ['BPI' => 1 / $data['bpi'][$date->format('Y-m-d')]];
        }

        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'Coin Desk';
    }

    /**
     * {@inheritDoc}
     */
    public function getSourceCurrency(): string
    {
        return self::SOURCE_CURRENCY;
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority(): int
    {
        return -1;
    }
}