<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClusterRequest extends FormRequest
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
            'name' => [
                'required'
            ],
            'endpoint' => [
                'required'
            ],
            'auth_type' => [
                'required',
                'in:P,T'
            ],
            'token' => [
                'nullable',
                'required_if:auth_type,T'
            ],
            'timeout' => [
                'nullable',
                'integer'
            ],
        ];
    }
}
