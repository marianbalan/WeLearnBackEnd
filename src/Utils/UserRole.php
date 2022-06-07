<?php

namespace App\Utils;

use MyCLabs\Enum\Enum;

/**
 * @extends Enum<string>
 * @method static self USER()
 * @method static self STUDENT()
 * @method static self TEACHER()
 * @method static self CLASS_MASTER()
 * @method static self DIRECTOR()
 */
class UserRole extends Enum
{
    public const USER = 'ROLE_USER';
    public const STUDENT = 'ROLE_STUDENT';
    public const TEACHER = 'ROLE_TEACHER';
    public const CLASS_MASTER = 'ROLE_CLASS_MASTER';
    public const DIRECTOR = 'ROLE_DIRECTOR';
}
