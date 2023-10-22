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

namespace App\Service\Exception;

use Throwable;

class NotEnoughNotesException extends ApplicationException
{
    public function __construct(string $message = 'Not enough notes.', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
