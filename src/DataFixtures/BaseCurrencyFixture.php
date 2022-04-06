<?php

namespace App\DataFixtures;

use App\Manager\CurrencyManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class BaseCurrencyFixture extends Fixture
{
    protected CurrencyManager $currencyManager;
    private string $defaultCurrency;

    public function __construct(CurrencyManager $currencyManager, string $defaultCurrency)
    {
        $this->currencyManager = $currencyManager;
        $this->defaultCurrency = $defaultCurrency;
    }

    public function load(ObjectManager $manager)
    {
        $currency = $this->currencyManager->create($this->defaultCurrency, 1);
        $this->currencyManager->update($currency);
    }
}