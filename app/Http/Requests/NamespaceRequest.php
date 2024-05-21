<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NamespaceRequest extends FormRequest
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
            // MAIN INFO
            'name' => [
                'required',
                'regex:/^[a-z0-9]([-a-z0-9]*[a-z0-9])?$/'
            ],
            
            // NOTES
            'key_labels.*' => [
                'required',
                'regex:/^(([A-Za-z0-9][-A-Za-z0-9_.]*)?[A-Za-z0-9])?$/'
            ],
            'value_labels.*' => [
                'required',
            ],
            'key_annotations.*' => [
                'required',
                'regex:/^([A-Za-z0-9][-A-Za-z0-9_.]*)?[A-Za-z0-9]$/'
            ],
            'value_annotations.*' => [
                'required',
            ],
            
            // NAMESPACE EXTRAS
            'finalizers.*' => [
                'required',
                'regex:/^([A-Za-z0-9][-A-Za-z0-9_.]*)?[A-Za-z0-9]$/'
            ],
        ];
    }

    public function messages(): array
{
    return [
        // MAIN INFO
        'name.required' => 'The name field is required.',
        'name.regex' => 'The name field must start with a lowercase letter or number and can contain dashes.',

        // NOTES
        'key_labels.*.required' => 'Each label key is required.',
        'key_labels.*.regex' => 'Each label key must be a valid DNS subdomain.',
        'value_labels.*.required' => 'Each label value is required.',

        'key_annotations.*.required' => 'Each annotation key is required.',
        'key_annotations.*.regex' => 'Each annotation key must be a valid DNS subdomain.',
        'value_annotations.*.required' => 'Each annotation value is required.',

        // NAMESPACE EXTRAS
        'finalizers.*.required' => 'Each finalizer is required.',
        'finalizers.*.regex' => 'Each finalizer must be a valid DNS subdomain.',
    ];
}

}
