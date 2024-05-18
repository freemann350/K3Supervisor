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
                Rule::in('Always','OnFailure','Never')
            ],
            'graceperiod' => [
                'nullable',
                'integer'
            ]
        ];
    }
}
