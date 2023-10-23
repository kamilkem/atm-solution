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

use App\Service\Exception\ApplicationException;
use App\Service\Exception\NoteInvalidArgumentException;
use App\Service\Exception\NotEnoughNotesException;
use App\Service\Exception\NoteUnavailableException;

final readonly class WithdrawProcessor
{
    public function __construct(private NoteStorage $noteStorage)
    {
    }

    /**
     * @throws ApplicationException
     */
    public function process(int $amount): array
    {
        if (!$amount) {
            return [];
        }

        if ($amount < 0) {
            throw new NoteInvalidArgumentException();
        }

        if ($this->noteStorage->totalValue() < $amount) {
            throw new NotEnoughNotesException();
        }

        return $this->handleTransaction($amount);
    }

    /**
     * @throws ApplicationException
     */
    private function handleTransaction(int $amount): array
    {
        $availableNotes = $this->noteStorage->available();

        foreach ($availableNotes as $note => $quantity) {
            for ($i = 0; $i < $quantity; $i++) {
                $newAmount = $amount - $note;

                if (0 > $newAmount) {
                    break;
                }

                $this->noteStorage->addTransaction($note);
                $amount = $newAmount;

                if (0 === $amount) {
                    return $this->noteStorage->finishTransaction();
                }
            }
        }

        $this->noteStorage->cancelTransaction();

        throw new NoteUnavailableException();
    }
}
