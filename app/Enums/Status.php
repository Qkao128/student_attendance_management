<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class Status extends Enum
{
    const Pending = 0;
    const InComplete = 1;
    const Completed = 2;
}
