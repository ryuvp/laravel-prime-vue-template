<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, Filterable;

    protected $guard_name = 'api';

    /**
     * Sex Map
     */
    const sexMap = [
        0 => 'Male',
        1 => 'Female'
    ];

    /**
     * Status Map
     */
    const statusMap = [
        0 => 'active',
        1 => 'inactive'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'birthdate'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'email',
        'password',
        'sex',
        'birthdate',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    public $appends = ['age', 'sex_format', 'status_format'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'updated_at',
        'deleted_at',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getSexFormatAttribute()
    {
        return self::sexMap[$this->sex];
    }

    public function getStatusFormatAttribute()
    {
        return self::statusMap[$this->status];
    }

    public function getAgeAttribute()
    {
        if (empty($this->birthdate)) {
            return 0;
        }
        return Carbon::now()->diffInYears($this->birthdate);
    }
}
