<?php

namespace App;

use App\Services\UserService;
use Eloquent;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Sofa\Eloquence\Mappable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @mixin Eloquent
 */
class User extends Authenticatable
{
    protected $primaryKey = 'u_id';
    protected $table = 'users';

    const CREATED_AT = 'u_created_at';
    const UPDATED_AT = 'u_updated_at';
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected array $maps = [
        'id' => 'u_id',
        'password' => 'u_password',
        'email' => 'u_email',
        'email_verified_at' => 'u_email_verified_at',
        'remember_token' => 'u_remember_token',
        'created_at' => 'u_created_at',
        'updated_at' => 'u_updated_at',
        'role' => 'u_role',
        'first_name' => 'u_first_name',
        'last_name' => 'u_last_name',
        'active' => 'u_active',
        'last_login' => 'u_last_login',
        'position' => 'u_position',
        'department' => 'u_department'


    ];
    protected $fillable = [
        'email', 'password','email_verified_at','remember_token',
        'created_at','updated_at','role','first_name','last_name','active','last_login','position','department'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $attributes = [
        'active' => true,
        'department'=>'',
        'position'=>'',

    ];



}
