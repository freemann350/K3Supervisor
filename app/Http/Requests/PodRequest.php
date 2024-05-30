<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PodRequest extends FormRequest
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
            'namespace' => [
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

            // CONTAINERS
            'containers' => [
                'required'
            ],
            'containers.*.name' => [
                'required',
                'regex:/^(([A-Za-z0-9][-A-Za-z0-9_.]*)?[A-Za-z0-9])?$/'
            ],
            'containers.*.image' => [
                'required',
            ],
            'containers.*.imagePullPolicy' => [
                'required',
                'in:IfNotPresent,Always,Never'
            ],
            'containers.*.ports.*' => [
                'required',
                'regex:/^([0-9]+)$/'
            ],
            'containers.*.env.key.*' => [
                'required',
                'regex:/^(([A-Za-z0-9][-A-Za-z0-9_.]*)?[A-Za-z0-9])?$/'
            ],  
            'containers.*.env.value.*' => [
                'required'
            ],

            //EXTRAS
            'restartpolicy' => [
                'required',
                'in:Always,OnFailure,Never'
            ],
            'graceperiod' => [
                'nullable',
                'integer'
            ]
        ];
    }

    public function messages(): array
{
    return [
        // MAIN INFO
        'name.required' => 'The name field is required.',
        'name.regex' => 'The name field must start with a lowercase letter or number and can contain dashes.',
        'namespace.required' => 'The namespace field is required.',
        'namespace.regex' => 'The namespace field must start with a lowercase letter or number and can contain dashes.',

        // NOTES
        'key_labels.*.required' => 'Each label key is required.',
        'key_labels.*.regex' => 'Each label key must be a valid DNS subdomain.',
        'value_labels.*.required' => 'Each label value is required.',
        'key_annotations.*.required' => 'Each annotation key is required.',
        'key_annotations.*.regex' => 'Each annotation key must be a valid DNS subdomain.',
        'value_annotations.*.required' => 'Each annotation value is required.',

        // CONTAINERS
        'containers.required' => 'The containers field is required.',
        'containers.*.name.required' => 'The container name is required.',
        'containers.*.name.regex' => 'The container name must be a valid DNS subdomain.',
        'containers.*.image.required' => 'The container image is required.',
        'containers.*.ports.*.required' => 'The container port is required.',
        'containers.*.ports.*.regex' => 'The container port must be a number.',
        'containers.*.env.key.*.required' => 'The environment variable key is required.',
        'containers.*.env.key.*.regex' => 'The environment variable key must be a valid DNS subdomain.',
        'containers.*.env.value.*.required' => 'The environment variable value is required.',

        // EXTRAS
        'restartpolicy.required' => 'The restart policy field is required.',
        'restartpolicy.in' => 'The restart policy must be one of the following: Always, OnFailure, or Never.',
        'graceperiod.nullable' => 'The grace period field can be null.',
        'graceperiod.integer' => 'The grace period must be an integer.',
    ];
}
}
