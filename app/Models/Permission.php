<?php

namespace App\Models;

class Permission extends \Spatie\Permission\Models\Permission
{
    public $guard_name = 'api';
}
