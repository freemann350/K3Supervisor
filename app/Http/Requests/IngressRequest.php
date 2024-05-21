<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IngressRequest extends FormRequest
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

            // RULES
            'rules' => [
                'required',
                'array'
            ],
            'rules.*.host' => [
                'nullable',
                'regex:/^([a-zA-Z0-9]([-a-zA-Z0-9]*[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/'
            ],
            
            // PATH
            'rules.*.path' => [
                'required',
            ],
            'rules.*.path.pathName.*' => [
                'required',
                'regex:/^\/.*$/'
            ],
            'rules.*.path.pathType.*' => [
                'required',
                'in:Exact,Prefix,ImplementationSpecific'
            ],
            'rules.*.path.serviceName.*' => [
                'required',
                'regex:/^[a-z0-9]([-a-z0-9]*[a-z0-9])?$/'
            ],
            'rules.*.path.portNumber.*' => [
                'required',
                'integer',
                'min:1',
                'max:65535'
            ],
            

            // INGRESS EXTRAS
            'defaultBackendName' => [
                'nullable',
                'regex:/^[a-z0-9]([-a-z0-9]*[a-z0-9])?$/'
            ],
            'defaultBackendPort' => [
                'nullable',
                'required_with:defaultBackendName',
                'integer',
                'min:1',
                'max:65535'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            // MAIN INFO
            'name.required' => 'The name field is required.',
            'name.regex' => 'The name field must contain only lowercase alphanumeric characters or hyphens and cannot start or end with a hyphen.',
            'namespace.required' => 'The namespace field is required.',
            'namespace.regex' => 'The namespace field must contain only lowercase alphanumeric characters or hyphens and cannot start or end with a hyphen.',
        
            // NOTES
            'key_labels.*.required' => 'The key labels field is required.',
            'key_labels.*.regex' => 'Each key label must contain only alphanumeric characters, hyphens, underscores, or periods, and cannot start or end with a period.',
            'value_labels.*.required' => 'The value labels field is required.',
            'key_annotations.*.required' => 'The key annotations field is required.',
            'key_annotations.*.regex' => 'Each key annotation must contain only alphanumeric characters, hyphens, underscores, or periods, and cannot start or end with a period.',
            'value_annotations.*.required' => 'The value annotations field is required.',
        
            // RULES
            'rules.required' => 'The rules field is required.',
            'rules.array' => 'The rules field must be an array.',
            'rules.*.host.regex' => 'The host field must be a valid domain name.',

            // PATH
            'rules.*.path.required' => 'The path field is required.',
            'rules.*.path.pathName.*.required' => 'The pathName field is required.',
            'rules.*.path.pathName.*.regex' => 'The pathName field must start with a forward slash.',
            'rules.*.path.pathType.*.required' => 'The pathType field is required.',
            'rules.*.path.pathType.*.in' => 'The pathType field must be one of: Exact, Prefix, ImplementationSpecific.',
            'rules.*.path.serviceName.*.required' => 'The serviceName field is required.',
            'rules.*.path.serviceName.*.regex' => 'The serviceName field must contain only lowercase alphanumeric characters or hyphens and cannot start or end with a hyphen.',
            'rules.*.path.portNumber.*.required' => 'The portNumber field is required.',
            'rules.*.path.portNumber.*.integer' => 'The portNumber field must be an integer.',
            'rules.*.path.portNumber.*.min' => 'The portNumber field must be at least :min.',
            'rules.*.path.portNumber.*.max' => 'The portNumber field must not exceed :max.',
        
            // INGRESS EXTRAS
            'defaultBackendName.regex' => 'The defaultBackendName field must contain only lowercase alphanumeric characters or hyphens and cannot start or end with a hyphen.',
            'defaultBackendPort.integer' => 'The defaultBackendPort field must be an integer.',
            'defaultBackendPort.min' => 'The defaultBackendPort field must be at least :min.',
            'defaultBackendPort.max' => 'The defaultBackendPort field must not exceed :max.'
        ];
    }
}