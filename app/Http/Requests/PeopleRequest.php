<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeopleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'identification' => ['required','string','max:50'],
            'firstname'      => ['required','string','max:100'],
            'lastname'       => ['required','string','max:100'],
            'address'        => ['nullable','string','max:255'],
            'cellphone'      => ['nullable','string','max:30'],
            'email'          => ['nullable','email','max:150'],
            'gender'         => ['nullable','string','max:20'],
            'birthday'       => ['nullable','date'],       // YYYY-MM-DD
            'sex'            => ['nullable','string','max:10'],
            'status'         => ['nullable','string','max:20'],
        ];
    }
}
