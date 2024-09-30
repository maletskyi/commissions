<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\DTOs\Transaction;
use App\Exceptions\FileCanNotBeOpenedException;
use App\Services\TransactionsParser\TransactionsParser;
use PHPUnit\Framework\TestCase;

class TransactionsParserTest extends TestCase
{
    public function testUserCanGetTransactionsFromFile(): void
    {
        $testFile = sys_get_temp_dir() . '/test.txt';

        file_put_contents(
            $testFile,
            '{"bin":"45717360","amount":"100.00","currency":"EUR"}' . PHP_EOL .
            '{"bin":"516793","amount":"50.00","currency":"USD"}' . PHP_EOL .
            '{"bin":"45417360","amount":"10000.00","currency":"JPY"}' . PHP_EOL .
            PHP_EOL . '       ' . PHP_EOL .
            '{"bin":"41417360","amount":"130.00","currency":"USD"}' . PHP_EOL .
            '{"bin":"4745030","amount":"2000.00","currency":"GBP"}' . PHP_EOL . PHP_EOL
        );

        $transactions = (new TransactionsParser())->parseFile($testFile);

        self::assertEquals(
            [
                new Transaction(45717360, 100.00, 'EUR'),
                new Transaction(516793, 50.00, 'USD'),
                new Transaction(45417360, 10000.00, 'JPY'),
                new Transaction(41417360, 130.00, 'USD'),
                new Transaction(4745030, 2000.00, 'GBP'),
            ],
            $transactions
        );

        unlink($testFile);
    }

    public function testUserGetExceptionIfFileNotExists(): void
    {
        $this->expectException(FileCanNotBeOpenedException::class);

        (new TransactionsParser())->parseFile(sys_get_temp_dir() . '/test.txt');
    }
}
