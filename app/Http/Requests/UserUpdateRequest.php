<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
                "required"
            ],
            'email' => [
                "required",
                "email"
            ],
            'role' => [
                "nullable",
                "string",
                "in:A,U"
            ],
            
            // RESOURCES
            'namespaces' => [
                "nullable",
                'in:true'
            ],
            'pods' => [
                "nullable",
                'in:true'
            ],
            'deployments' => [
                "nullable",
                'in:true'
            ],
            'services' => [
                "nullable",
                'in:true'
            ],
            'ingresses' => [
                "nullable",
                'in:true'
            ],
            'customresources' => [
                "nullable",
                'in:true'
            ],
            'backups' => [
                "nullable",
                'in:true'
            ],

            // VERBS
            'create' => [
                "nullable",
                'in:true'
            ],
            'delete' => [
                "nullable",
                'in:true'
            ],
        ];
    }
}
