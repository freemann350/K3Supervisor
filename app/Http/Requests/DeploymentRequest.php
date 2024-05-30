<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeploymentRequest extends FormRequest
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

            //POD MATCHING
            'key_matchLabels' => [
                'required'
            ],
            'value_matchLabels' => [
                'required'
            ],
            'key_matchLabels.*' => [
                'required',
                'regex:/^(([A-Za-z0-9][-A-Za-z0-9_.]*)?[A-Za-z0-9])?$/'
            ],
            'value_matchLabels.*' => [
                'required'
            ],
            'replicas' => [
                'required',
                'integer'
            ],

            // TEMPLATE LABELS
            'key_templateLabels' => [
                'required'
            ],
            'value_templateLabels' => [
                'required'
            ],
            'key_templateLabels.*' => [
                'required',
                'regex:/^(([A-Za-z0-9][-A-Za-z0-9_.]*)?[A-Za-z0-9])?$/'
            ],
            'value_templateLabels.*' => [
                'required'
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
            'strategy' => [
                'required',
                'in:Auto,RollingUpdate,Recreate'
            ],
            'maxUnavailable' => [
                'required_if:strategy,RollingUpdate',
                'regex:/^(100|[1-9]?[0-9])%?$|^(0*[1-9][0-9]*)$/'
            ],
            'maxSurge' => [
                'required_if:strategy,RollingUpdate',
                'regex:/^(100|[1-9]?[0-9])%?$|^(0*[1-9][0-9]*)$/'
            ],
            'minReadySeconds' => [
                'nullable',
                'integer'
            ],
            'revisionHistoryLimit' => [
                'nullable',
                'integer'
            ],
            'progressDeadlineSeconds' => [
                'nullable',
                'integer'
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

            // POD MATCHING
            'key_matchLabels.required' => 'Label Matching is required.',
            'value_matchLabels.required' => 'The value_matchLabels field is required.',
            'key_matchLabels.*.required' => 'The match label key is required.',
            'key_matchLabels.*.regex' => 'The match label key must be a valid DNS subdomain.',
            'value_matchLabels.*.required' => 'The match label value is required.',
            'replicas.required' => 'The replicas field is required.',
            'replicas.integer' => 'The replicas field must be an integer.',

            // TEMPLATE LABELS
            'key_templateLabels.required' => 'The Template Labels are required.',
            'value_templateLabels.required' => 'The value_templateLabels field is required.',
            'key_templateLabels.*.required' => 'The template label key is required.',
            'key_templateLabels.*.regex' => 'The template label key must be a valid DNS subdomain.',
            'value_templateLabels.*.required' => 'The template label value is required.',

            // CONTAINERS
            'containers.required' => 'The containers field is required.',
            'containers.*.name.required' => 'The container name is required.',
            'containers.*.name.regex' => 'The container name must be a valid DNS subdomain.',
            'containers.*.image.required' => 'The container image is required.',
            'containers.*.imagePullPolicy.required' => 'The container image pull policy is required.',
            'containers.*.imagePullPolicy.in' => 'The container image pull policy one of the following: IfNotPresent, Always, Never',
            'containers.*.ports.*.required' => 'The container port is required.',
            'containers.*.ports.*.regex' => 'The container port must be a number.',
            'containers.*.env.key.*.required' => 'The environment variable key is required.',
            'containers.*.env.key.*.regex' => 'The environment variable key must be a valid DNS subdomain.',
            'containers.*.env.value.*.required' => 'The environment variable value is required.',

            // EXTRAS
            'strategy.required' => 'The strategy field is required.',
            'strategy.in' => 'The strategy must be one of the following: Auto, RollingUpdate, or Recreate.',
            'maxUnavailable.required_if' => 'The maxUnavailable field is required when the strategy is RollingUpdate.',
            'maxUnavailable.regex' => 'The maxUnavailable field must be a valid number or percentage.',
            'maxSurge.required_if' => 'The maxSurge field is required when the strategy is RollingUpdate.',
            'maxSurge.regex' => 'The maxSurge field must be a valid number or percentage.',
            'minReadySeconds.nullable' => 'The minReadySeconds field can be null.',
            'minReadySeconds.integer' => 'The minReadySeconds field must be an integer.',
            'revisionHistoryLimit.nullable' => 'The revisionHistoryLimit field can be null.',
            'revisionHistoryLimit.integer' => 'The revisionHistoryLimit field must be an integer.',
            'progressDeadlineSeconds.nullable' => 'The progressDeadlineSeconds field can be null.',
            'progressDeadlineSeconds.integer' => 'The progressDeadlineSeconds field must be an integer.',
        ];
    }
}
