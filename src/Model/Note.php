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

use function in_array;

class Note implements NoteInterface
{
    private int $value;

    public function __construct(int $value)
    {
        if (!in_array($value, self::AVAILABLE_VALUES)) {
            throw new \LogicException();
        }

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
