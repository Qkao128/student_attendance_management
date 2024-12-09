<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class Status extends Enum
{
    const Present = 0;
    const Absence = 1;
    const Late = 2;
    const Medical = 3;
    const LeaveApproval = 4;
}
