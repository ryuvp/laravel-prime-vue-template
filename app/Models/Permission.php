<?php

namespace App\Models;

class Permission extends \Spatie\Permission\Models\Permission
{
    public $guard_name = 'api';

    const categoryMap = [
        0 => 'section',
        1 => 'menu',
        2 => 'menu_link',
    ];

    protected $fillable = ['name', 'guard_name', 'description', 'route', 'icon', 'category', 'parent_id'];

    public $appends = ['category_name'];

    public function getCategoryNameAttribute()
    {
        return self::categoryMap[$this->category];
    }

    public function parent()
    {
        return $this->belongsTo(Permission::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Permission::class, 'parent_id');
    }
}
