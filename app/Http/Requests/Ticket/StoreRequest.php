<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'subject' => ['required', 'max:255'],
            // 'department_id' => ['exclude_if:department_id,null', 'exists:departments,id'],
            'department_id' => ['required'],
            'sub_department_id' => ['required'],
            'department_location_id' => ['required'],
            'body' => ['required'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'subject.required' => __('The :attribute field is required', ['attribute' => __('subject')]),
            'subject.max' => __('The :attribute may not be greater than :max characters', ['attribute' => __('subject'), 'max' => 255]),


            // 'department_id.exists' => __('The selected :attribute is invalid', ['attribute' => __('department')]),

            'department_id.required' => __('Please select department'),
            'sub_department_id.required' => __('Please select project'),
            'department_location_id.required' => __('Please select location'),
            
            // 'body.required' => __('The :attribute field is required', ['attribute' => __('body')]),
            'body.required' => __('Please enter ticket body'),
        ];
    }
}
