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

namespace App\Tests\Unit;

use App\Service\Exception\NoteInvalidArgumentException;
use App\Service\Exception\NotEnoughNotesException;
use App\Service\Exception\NoteUnavailableException;
use App\Service\NoteGenerator;
use App\Service\NoteStorage;
use App\Service\WithdrawProcessor;
use PHPUnit\Framework\TestCase;

class WithdrawProcessorTest extends TestCase
{
    public function test001processReceiveSingleNote(): void
    {
        $storage = $this->initStorage();

        $processor = new WithdrawProcessor($storage);
        $withdraw = $processor->process(200);

        $this->assertEquals(['100' => 2], $withdraw);
    }

    public function test002processReceiveMultipleNotes(): void
    {
        $storage = $this->initStorage();

        $processor = new WithdrawProcessor($storage);
        $withdraw = $processor->process(80);

        $this->assertEquals(['50' => 1, '20' => 1, '10' => 1], $withdraw);
    }

    public function test003processReceiveEmptyArray()
    {
        $storage = $this->initStorage();

        $processor = new WithdrawProcessor($storage);
        $withdraw = $processor->process(0);

        $this->assertEquals([], $withdraw);
    }

    public function test004processThrowsNotEnoughNotes(): void
    {
        $this->expectException(NotEnoughNotesException::class);

        $storage = $this->initStorage();

        $processor = new WithdrawProcessor($storage);
        $processor->process(3000);
    }

    public function test005processThrowsNoteInvalidArgument(): void
    {
        $this->expectException(NoteInvalidArgumentException::class);

        $storage = $this->initStorage();

        $processor = new WithdrawProcessor($storage);
        $processor->process(-130);
    }

    public function test006processThrowsNoteUnavailable(): void
    {
        $this->expectException(NoteUnavailableException::class);

        $storage = $this->initStorage();

        $processor = new WithdrawProcessor($storage);
        $processor->process(125);
    }

    public function test007processTwoTransactionsProperly(): void
    {
        $storage = $this->initStorage();

        $processor = new WithdrawProcessor($storage);
        $processor->process(200);

        $this->assertEquals(1600, $storage->totalValue());

        $processor->process(200);

        $this->assertEquals(1400, $storage->totalValue());
    }

    public function test008processTwoTransactionsThrowsNotEnoughNotes()
    {
        $storage = $this->initStorage();

        $processor = new WithdrawProcessor($storage);
        $processor->process(1700);

        $this->expectException(NotEnoughNotesException::class);
        $processor->process(200);
    }

    private function initStorage(): NoteStorage
    {
        $notes = [
            ['value' => 100, 'quantity' => 10],
            ['value' => 50, 'quantity' => 10],
            ['value' => 20, 'quantity' => 10],
            ['value' => 10, 'quantity' => 10],
        ];

        $storage = new NoteStorage();
        $generator = new NoteGenerator();

        foreach ($generator->generate($notes) as $note) {
            $storage->addNote($note);
        }

        return $storage;
    }
}
