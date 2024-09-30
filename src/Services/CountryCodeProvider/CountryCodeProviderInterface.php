<?php

declare(strict_types=1);

namespace App\Services\CountryCodeProvider;

interface CountryCodeProviderInterface
{
    public function getCountryCodeByBin(int $bin): string;
}
