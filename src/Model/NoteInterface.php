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

namespace App\Model;

interface NoteInterface
{
    public const AVAILABLE_VALUES = [
        100,
        50,
        20,
        10,
    ];

    public function getValue(): int;
}
