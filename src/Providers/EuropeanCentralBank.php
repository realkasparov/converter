<?php
declare(strict_types=1);

namespace App\Providers;

use App\Helper\StringUtils;

final class EuropeanCentralBank extends HttpService implements ExchangeRateProvider
{
    private const SOURCE_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    private const SOURCE_CURRENCY = 'EUR';

    /**
     * {@inheritDoc}
     */
    public function importRates(): array
    {
        $xml = $this->request(self::SOURCE_URL);

        $dom = StringUtils::xmlToElement($xml);

        $node = $dom->xpath('//gesmes:Envelope')[0]->Cube->Cube->Cube;
        $currencies = [];
        foreach ($node->xpath('//@currency') as $currency) {
            $currencies[] = (string)$currency;
        }
        $rates = [];
        foreach ($node->xpath('//@rate') as $rate) {
            $rates[] = (float)$rate;
        }

        return \array_combine($currencies, $rates);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'European Central Bank';
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
        return -2;
    }
}