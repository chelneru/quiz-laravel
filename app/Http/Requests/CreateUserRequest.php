<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password'=>'required|min:8|max:50',
            'email'=>'required|email|max:190',
            'role'=>'required|integer|min:1|max:3',
            'first_name'=>'required|max:255',
            'last_name'=>'required|max:255',
            'position'=>'max:100',
            'department'=>'max:100'

        ];
    }
}
