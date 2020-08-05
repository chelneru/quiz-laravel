<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public $timestamps = true;
    protected $table ='questions';
    protected $primaryKey = 'questions_id';

    protected $fillable = [
        'question_id',
        'question_u_id',
        'question_text',
        'question_right_answer',
        'question_active',
        'question_created_on',

    ];
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'question_created_on'
    ];
}
