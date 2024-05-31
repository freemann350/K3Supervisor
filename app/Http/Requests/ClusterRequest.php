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
                'required',
                'regex:/^[a-zA-Z0-9_\-\.]+$/'
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
    
    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.regex' => 'The name field must not contain spaces or special characters not typically used in file names.',
            'endpoint.required' => 'The endpoint field is required.',
            'auth_type.required' => 'The authentication type field is required.',
            'auth_type.in' => 'The authentication type must be either P (password) or T (token).',
            'token.required_if' => 'The token field is required when authentication type is set to T (token).',
            'timeout.integer' => 'The timeout field must be an integer.',
        ];
    }
}
