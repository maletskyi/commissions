<?php

declare(strict_types=1);

namespace App\Services\CommissionCalculator;

use App\DTOs\Transaction;

interface CommissionCalculatorInterface
{
    public function calculate(Transaction $transaction): float;
}
