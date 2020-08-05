<?php
/**
 * Created by PhpStorm.
 * User: au614698
 * Date: 29-10-2018
 * Time: 16:04
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLogin  extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table ='user_logins';
    protected $primaryKey = 'ut_id';

    protected $fillable = [
        'ut_id',
        'ut_u_id',
        'ut_time'
    ];
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'ut_time'
    ];


}