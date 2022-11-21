<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROL_OWNER = -1;
    const ROL_ADMIN = 1;
    const ROL_ADMIN_PERISHABLE = 2;
    const ROL_AUDITOR = 3;
    const ROL_INVESTIGATOR = 4;

    public static function getAllRoles() {
        return [
            self::ROL_OWNER,
            self::ROL_ADMIN,
            self::ROL_ADMIN_PERISHABLE,
            self::ROL_AUDITOR,
            self::ROL_INVESTIGATOR,
        ];
    }

    public static function getCompanyAdminRoles() {
        return [
            self::ROL_ADMIN,
            self::ROL_ADMIN_PERISHABLE,
            self::ROL_AUDITOR,
        ];
    }
    
    public static function getCompanyRoles() {
        return [
            self::ROL_ADMIN,
            self::ROL_ADMIN_PERISHABLE,
            self::ROL_AUDITOR,
            self::ROL_INVESTIGATOR,
        ];
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'photo',
        'phone',
        'email',
        'password',
        'rol',
        'company_id',
        'enabled',
        'start',
        'end'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
