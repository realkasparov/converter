<?php
declare(strict_types=1);

namespace App\Command;

use App\Manager\CurrencyManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ConverterConvertCommand extends Command
{
    protected static $defaultName = 'converter:convert';
    protected static string $defaultDescription = 'Convert currency';
    protected int $rateRound;

    private CurrencyManager $currencyManager;

    public function __construct(CurrencyManager $currencyManager, int $rateRound, string $name = null)
    {
        parent::__construct($name);
        $this->currencyManager = $currencyManager;
        $this->rateRound = $rateRound;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('from', InputArgument::REQUIRED, 'The currency you want to convert from [f.e. USD]')
            ->addArgument('to', InputArgument::REQUIRED, 'The currency you want to convert to [f.e. EUR]')
            ->addArgument('amount', InputArgument::OPTIONAL, 'The amount you want to convert')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $from = $input->getArgument('from');
        $to = $input->getArgument('to');
        $fromAmount = $input->getArgument('amount') ? (float)$input->getArgument('amount') : 1.0;

        try {
            $fromCurrency = $this->currencyManager->getByName($from);
            $toCurrency = $this->currencyManager->getByName($to);

            if (!$fromCurrency) {
                return $this->noCurrencyWarning($io, $from);
            }

            if (!$toCurrency) {
                return $this->noCurrencyWarning($io, $to);
            }

            $toAmount = $fromAmount / $fromCurrency->getRate() * $toCurrency->getRate();

            $format = \sprintf('%s %s = %s %s', $this->getSpec($fromAmount), '%s', $this->getSpec($toAmount), '%s');
            $io->text(\sprintf($format, $fromAmount, $from, $toAmount, $to));
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return 1;
        }

        return 0;
    }

    private function getSpec(float $arg): string {
        return (int)$arg === 0 ? '%.' . $this->rateRound . 'h' : '%.' . $this->rateRound . 'f';
    }

    protected function noCurrencyWarning(SymfonyStyle $io, string $currency): int
    {
        $io->warning(\sprintf('%s currency does not existed! Add source with this currency first!', $currency));
        return 1;
    }
}
