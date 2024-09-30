<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\CommissionCalculator\CommissionCalculatorInterface;
use App\Services\TransactionsParser\TransactionsParserInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(name: 'commissions')]
class CalculateCommissions extends Command
{
    public function __construct(
        private readonly TransactionsParserInterface $transactionsParser,
        private readonly CommissionCalculatorInterface $commissionCalculator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'File to process');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $file = $input->getArgument('file');

        try {
            $transactions = $this->transactionsParser->parseFile($file);

            foreach ($transactions as $transaction) {
                $style->text((string) $this->commissionCalculator->calculate($transaction));
            }
        } catch (Throwable $throwable) {
            $style->error('Error happened: ' . $throwable->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
