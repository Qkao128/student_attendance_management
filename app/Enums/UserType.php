<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserType extends Enum
{
    const SuperAdmin = 0;
    const Admin = 1;
    const Monitor = 2;
}
