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

namespace App\Service;

use App\Model\NoteInterface;

use function array_splice;
use function count;
use function krsort;

final class NoteStorage
{
    private array $data = [];
    private array $transaction = [];

    public function addNote(NoteInterface $note): void
    {
        $this->data[(string) $note->getValue()][] = $note;
        krsort($this->data);
    }

    public function totalValue(): int
    {
        $value = 0;
        foreach ($this->data as $key => $notes) {
            $value += (int) $key * count($notes);
        }

        return $value;
    }

    public function available(): array
    {
        $map = [];
        foreach ($this->data as $key => $notes) {
            $map[$key] = count($notes);
        }

        return $map;
    }

    public function addTransaction(int $noteValue): void
    {
        if (!isset($this->transaction[$noteValue])) {
            $this->transaction[$noteValue] = 0;
        }

        $this->transaction[$noteValue]++;
    }

    public function finishTransaction(): array
    {
        $available = $this->available();
        $data = $this->data;
        $transaction = $this->transaction;

        foreach ($transaction as $noteValue => $qty) {
            if (!isset($available[$noteValue]) || $qty > $available[$noteValue]) {
                throw new \LogicException();
            }

            array_splice($data[$noteValue], 0, $qty);
        }

        $this->data = $data;
        $this->transaction = [];

        return $transaction;
    }

    public function cancelTransaction(): void
    {
        $this->transaction = [];
    }
}
