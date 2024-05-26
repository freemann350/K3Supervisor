<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BackupRequest extends FormRequest
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
            // RESOURCES
            'namespaces' => [
                'nullable',
                'in:true'
            ],
            'pods' => [
                'nullable',
                'in:true'
            ],
            'deployments' => [
                'nullable',
                'in:true'
            ],
            'services' => [
                'nullable',
                'in:true'
            ],
            'ingresses' => [
                'nullable',
                'in:true'
            ],

            // OPTIONS
            'excludeDefaultResources' => [
                'nullable',
                'in:true'
            ],
            'excludeDeploymentPods' => [
                'nullable',
                'in:true'
            ],
        ];
    }
}
