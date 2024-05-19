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
                'required',
            ],
            'nodePort.*' => [
                'required',
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
}
