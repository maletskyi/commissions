<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\DTOs\Transaction;
use App\Enums\EuCountryCodes;
use App\Services\CommissionCalculator\CommissionCalculator;
use App\Services\CountryCodeProvider\CountryCodeProviderInterface;
use App\Services\CurrencyRateProvider\CurrencyRateProviderInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class CommissionCalculatorTest extends TestCase
{
    #[DataProvider('countryCodesProvider')]
    public function testUserCanCalculateCommission(string $countryCode, bool $isEu): void
    {
        $countryCodeProvider = $this->createMock(CountryCodeProviderInterface::class);
        $countryCodeProvider->method('getCountryCodeByBin')->willReturn($countryCode);

        $rates = ['AED' => 3.939815];

        $currenciesProvider = $this->createMock(CurrencyRateProviderInterface::class);
        $currenciesProvider->method('getAllRates')->willReturn($rates);

        $calculator = new CommissionCalculator($countryCodeProvider, $currenciesProvider);

        $transaction = new Transaction(123, 105.41, 'AED');

        self::assertEquals(
            round($transaction->getAmount() / $rates[$transaction->getCurrency()] * ($isEu ? 0.01 : 0.02), 2),
            $calculator->calculate($transaction)
        );
    }

    public static function countryCodesProvider(): array
    {
        $codes = [];

        foreach (EuCountryCodes::cases() as $case) {
            $codes[$case->name] = [$case->name, true];
        }

        $codes['UA'] = ['UA', false];
        $codes['UK'] = ['UK', false];
        $codes['AS'] = ['AS', false];
        $codes['AF'] = ['AF', false];
        $codes['AL'] = ['AL', false];

        return $codes;
    }
}
