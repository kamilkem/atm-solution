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

use App\Model\Note;
use App\Model\NoteInterface;

final readonly class NoteGenerator
{
    /**
     * @return \Generator<NoteInterface>
     */
    public function generate(array $instruction = []): \Generator
    {
        foreach ($instruction as $item) {
            $value = $item['value'];
            $quantity = $item['quantity'];

            for ($i = 0; $i < $quantity; $i++) {
                yield new Note($value);
            }
        }
    }
}
