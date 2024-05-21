<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
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

            // SELECTOR
            'key_selectorLabels.*' => [
                'required',
                'regex:/^(([A-Za-z0-9][-A-Za-z0-9_.]*)?[A-Za-z0-9])?$/'
            ],
            'value_selectorLabels.*' => [
                'required',
            ],

            // PORTS
            'portName' => [
                'required'
            ],
            'portName.*' => [
                'required'
            ],
            'protocol' => [
                'required'
            ],
            'protocol.*' => [
                'required',
                'in:TCP,UDP'
            ],
            'port' => [
                'required',
            ],
            'port.*' => [
                'required',
                'numeric'
            ],
            'target' => [
                'required',
            ],
            'target.*' => [
                'required',
                'numeric'
            ],
            'nodePort' => [
                'required_if:type,Auto,NodePort,LoadBalancer,ExternalName',
            ],
            'nodePort.*' => [
                'required_if:type,Auto,NodePort,LoadBalancer,ExternalName',
                'nullable',
                'numeric',
                'between:30000,32767'
            ],

            // EXTRAS
            'type' => [
                'required',
                'in:Auto,ClusterIP,NodePort,LoadBalancer,ExternalName'
            ],
            'externalName' => [
                'required_if:type,ExternalName',
                'regex:/^(?=.{1,253}$)(?!-)[A-Za-z0-9-]{1,63}(?<!-)(\.[A-Za-z0-9-]{1,63})*$/'
            ],
            'externalTrafficPolicy' => [
                'required',
                'in:Auto,Cluster,Local'
            ],
            'sessionAffinity' => [
                'required',
                'in:Auto,None,ClientIP'
            ],
            'sessionAffinityTimeoutSeconds' => [
                'nullable',
                'gte:0',
                'regex:/^[a-z0-9]([-a-z0-9]*[a-z0-9])?$/'
            ],
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
        'key_labels.*.required' => 'The label key is required.',
        'key_labels.*.regex' => 'The label key must be a valid DNS subdomain.',
        'value_labels.*.required' => 'The label value is required.',
        'key_annotations.*.required' => 'The annotation key is required.',
        'key_annotations.*.regex' => 'The annotation key must be a valid DNS subdomain.',
        'value_annotations.*.required' => 'The annotation value is required.',

        // SELECTOR
        'key_selectorLabels.*.required' => 'The selector label key is required.',
        'key_selectorLabels.*.regex' => 'The selector label key must be a valid DNS subdomain.',
        'value_selectorLabels.*.required' => 'The selector label value is required.',

        // PORTS
        'portName.required' => 'The port name field is required.',
        'portName.*.required' => 'The port name field is required.',
        'protocol.required' => 'The protocol field is required.',
        'protocol.*.required' => 'The protocol field is required.',
        'protocol.*.in' => 'The protocol must be either TCP or UDP.',
        'port.required' => 'The port field is required.',
        'port.*.required' => 'The port field is required.',
        'port.*.numeric' => 'The port must be a number.',
        'target.required' => 'The target port field is required.',
        'target.*.required' => 'The target port field is required.',
        'target.*.numeric' => 'The target port must be a number.',
        'nodePort.required_if' => 'The nodePort field is required when type is Auto, NodePort, LoadBalancer, or ExternalName.',
        'nodePort.*.required_if' => 'The nodePort field is required when type is Auto, NodePort, LoadBalancer, or ExternalName.',
        'nodePort.*.numeric' => 'The nodePort must be a number.',
        'nodePort.*.between' => 'The nodePort must be between 30000 and 32767.',

        // EXTRAS
        'type.required' => 'The type field is required.',
        'type.in' => 'The type must be one of the following: Auto, ClusterIP, NodePort, LoadBalancer, or ExternalName.',
        'externalName.required_if' => 'The externalName field is required when type is ExternalName.',
        'externalName.regex' => 'The externalName must be a valid DNS name.',
        'externalTrafficPolicy.required' => 'The externalTrafficPolicy field is required.',
        'externalTrafficPolicy.in' => 'The externalTrafficPolicy must be one of the following: Auto, Cluster, or Local.',
        'sessionAffinity.required' => 'The sessionAffinity field is required.',
        'sessionAffinity.in' => 'The sessionAffinity must be one of the following: Auto, None, or ClientIP.',
        'sessionAffinityTimeoutSeconds.nullable' => 'The sessionAffinityTimeoutSeconds field must be null or a positive number.',
        'sessionAffinityTimeoutSeconds.gte' => 'The sessionAffinityTimeoutSeconds field must be greater than or equal to 0.',
        'sessionAffinityTimeoutSeconds.regex' => 'The sessionAffinityTimeoutSeconds must start with a lowercase letter or number and can contain dashes.',
    ];
}
}
