<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'dob' => 'nullable|date',
            'mobile' => 'nullable|numeric',
            'gender' => 'nullable|in:m,f,o,M,F,O',
            'bio' => 'nullable|string',
            'is_disciple' => 'nullable|in:true,false,TRUE,FALSE,True,False,1,0',
        ];
    }
}
