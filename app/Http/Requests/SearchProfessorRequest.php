<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchProfessorRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required_without_all:email,rfc,curp|string|nullable',
            'email' => 'required_without_all:name,rfc,curp|nullable',
            'rfc' => 'required_without_all:name,email,curp|alpha_num|nullable',
            'curp' => 'required_without_all:name,email,rfc|alpha_num|nullable',
        ];
    }

    public function messages() {
        return [
            'name.required_without_all' => "Introduzca al menos un campo.",
        ];
    }
}
