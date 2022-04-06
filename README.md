# Converter

Simple currency converter

## Index

* [Installation](#installation)
* [Usage](#usage)
* [Custom providers](#custom-providers)

## Installation

1. Clone repository
```
git clone https://github.com/realkasparov/converter.git
```

2. Copy .env and rename it to .env.local
```
cd converter

cp .env .env.local
```

3. Edit DEFAULT_CURRENCY and RATE_ROUND at .env.local if it is necessary


4. Start docker
```
cd docker

docker-compose up
```
During the startup initial fixture will be executed and will write default currency to the database.

## Usage

1. Run php-fpm container
```
cd docker 

docker-compose run php-fpm sh 
```
2. Execute commands:
<table>
   <tr>
      <td>`bin/console converter:import-data`</td>
      <td>Will import data from all predefined sources</td>
   </tr>
   <tr>
     <td>`bin/console converter:convert USD TRY 10`</td>
     <td>Will convert amount (10) from one currency (USD) to another (TRY). Amount is optional, default value is 1.</td>
   </tr>
</table>

## Custom providers
To add custom provider, extend method from App\Providers\HttpService if you need HttpClient and implement App\Providers\ExchangeRateProvider to import data by your provider.
``` php
<?php
declare(strict_types=1);

namespace App\Providers;

final class CustomProvider extends HttpService implements ExchangeRateProvider
{
    private const SOURCE_URL = '...';

    private const SOURCE_CURRENCY = 'EUR';

    public function importRates(): array
    {
        ...
    }

    public function getName(): string
    {
        return 'Custom Provider';
    }

    public function getSourceCurrency(): string
    {
        return self::SOURCE_CURRENCY;
    }

    public function getPriority(): int
    {
        return 0;
    }
}
```
