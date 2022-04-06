<?php
declare(strict_types=1);

namespace App\Command;

use App\Manager\CurrencyManager;
use App\Providers\ExchangeRateProvider;
use App\Providers\ProviderRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ConverterImportDataCommand extends Command
{
    protected static $defaultName = 'converter:import-data';
    protected static string $defaultDescription = 'Import data from sources';

    protected string $defaultCurrency;

    protected ProviderRegistry $providerRegistry;
    private CurrencyManager $currencyManager;

    /**
     * @throws \App\Exception\NotUpdateException
     */
    public function __construct(CurrencyManager $currencyManager, ProviderRegistry $providerRegistry, string $defaultCurrency, string $name = null)
    {
        parent::__construct($name);
        $this->currencyManager = $currencyManager;
        $this->defaultCurrency = $defaultCurrency;
        $this->providerRegistry = $providerRegistry;
    }

    protected function configure(): void
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var ExchangeRateProvider $provider */
        foreach ($this->providerRegistry->getAll() as $provider) {
            try {
                $data = $provider->importRates();
                $providerCurrency = $this->currencyManager->getByName($provider->getSourceCurrency());
                if (!$providerCurrency) {
                    $io->warning(\sprintf('No currency rate for your source. Add source with %s currency first!', $provider->getSourceCurrency()));
                    return 1;
                }
                $factor = $provider->getSourceCurrency() !== $this->defaultCurrency ? $providerCurrency->getRate() : 1;

                foreach ($data as $name => $rate) {
                    $rate *= $factor;
                    $currency = $this->currencyManager->getByName($name);
                    if ($currency) {
                        $currency->setRate($rate);
                    } else {
                        $currency = $this->currencyManager->create($name, $rate);
                    }
                    $this->currencyManager->update($currency);
                }
            } catch (\Exception $e) {
                $io->error($e->getMessage());
                return 1;
            }

            $io->text(\sprintf('Data from %s source has been successfully imported!', $provider->getName()));
        }

        $io->success('All data has been imported!');

        return 0;
    }
}
