<?php

namespace App\Models;

use Spatie\Permission\Models\Permission;

class Role extends \Spatie\Permission\Models\Role
{
    public $guard_name = 'api';
}
