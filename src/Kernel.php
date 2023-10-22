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

namespace App;

use App\Command\WithdrawCommand;
use App\Service\NoteGenerator;
use App\Service\NoteStorage;
use Symfony\Component\Console\Application;

use function dirname;

class Kernel
{
    public function __invoke(): void
    {
        $this->boot();
    }

    private function boot(): void
    {
        $config = $this->getConfig();

        $app = new Application($config['app_name'] ?? 'UNKNOWN', $config['app_version'] ?? 'UNKNOWN');
        $command = new WithdrawCommand($this->initStorage($config['notes']));

        $app->add($command);
        $app->setDefaultCommand($command->getName() ?? 'UNKNOWN', true);

        $app->run();
    }

    private function getConfig(): array
    {
        return require dirname(__DIR__) . '/config.php';
    }

    private function initStorage(array $notes = []): NoteStorage
    {
        $storage = new NoteStorage();
        $generator = new NoteGenerator();

        foreach ($generator->generate($notes) as $note) {
            $storage->addNote($note);
        }

        return $storage;
    }
}
