<?php

declare(strict_types=1);

namespace App\Services\TransactionsParser;

use App\DTOs\Transaction;
use App\Exceptions\FileCanNotBeOpenedException;
use JsonException;

class TransactionsParser implements TransactionsParserInterface
{
    /**
     * @param  string  $file
     * @return array<Transaction>
     * @throws FileCanNotBeOpenedException
     * @throws JsonException
     */
    public function parseFile(string $file): array
    {
        $transactions = [];

        $filePointer = file_exists($file) ? fopen($file, 'rb') : false;

        if ($filePointer === false) {
            throw new FileCanNotBeOpenedException(sprintf('The file "%s" can\'t be opened', $file));
        }

        while (($line = fgets($filePointer)) !== false) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            $transactions[] = $this->parseLine($line);
        }

        fclose($filePointer);

        return $transactions;
    }

    private function parseLine(string $line): Transaction
    {
        $transactionData = json_decode($line, true, 512, JSON_THROW_ON_ERROR);

        return new Transaction(
            (int) $transactionData['bin'],
            (float) $transactionData['amount'],
            $transactionData['currency']
        );
    }
}
