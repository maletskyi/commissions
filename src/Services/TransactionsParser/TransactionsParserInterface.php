<?php

declare(strict_types=1);

namespace App\Services\TransactionsParser;

use App\DTOs\Transaction;

interface TransactionsParserInterface
{
    /**
     * @param  string  $file
     * @return array<Transaction>
     */
    public function parseFile(string $file): array;
}