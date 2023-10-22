<?php

/**
 * This file is part of the atm-solution package.
 *
 * (c) Kamil Kozaczyński <kozaczynski.kamil@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Command;

use App\Service\Exception\ApplicationException;
use App\Service\NoteStorage;
use App\Service\WithdrawProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function is_numeric;

class WithdrawCommand extends Command
{
    public function __construct(private readonly NoteStorage $noteStorage)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('withdraw');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $processor = new WithdrawProcessor($this->noteStorage);

        while (true) {
            $io->note('ATM status: ' . $this->noteStorage->totalValue());

            $amount = $io->ask('Input amount to withdraw');

            if (!is_numeric($amount)) {
                $io->writeln('Type proper amount value;');

                continue;
            }

            try {
                $withdraw = $processor->process((int) $amount);

                $table = $io->createTable()->setHeaders(['letter', 'quantity']);
                foreach ($withdraw as $key => $value) {
                    $table->addRow([$key, $value]);
                }
                $table->render();
            } catch (ApplicationException $exception) {
                $io->error($exception->getMessage());
            }

            if (!$io->confirm('Do you want to continue?')) {
                return self::SUCCESS;
            }
        }
    }
}
