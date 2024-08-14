<?php

namespace App\Http\Requests\Dashboard\Ticket;

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
            'user_id' => ['required', 'exists:users,id'],            
            'department_id' => ['required', 'exists:departments,id'],
            'sub_department_id' => ['required'],
            'department_location_id' => ['required'],
            'status_id' => ['required', 'exists:statuses,id'],
            // 'priority_id' => ['required', 'exists:priorities,id'],
            'subject' => ['required', 'max:255'],
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
            'user_id.required' => __('The :attribute field is required', ['attribute' => __('Engineer/Customer')]),
            'user_id.exists' => __('The selected :attribute is invalid', ['attribute' => __('Engineer/Customer')]),

            'subject.required' => __('The :attribute field is required', ['attribute' => __('subject')]),
            'subject.max' => __('The :attribute may not be greater than :max characters', ['attribute' => __('subject'), 'max' => 255]),

            'department_id.required' => __('The :attribute field is required', ['attribute' => __('department')]),
            'department_id.exists' => __('The selected :attribute is invalid', ['attribute' => __('department')]),

            'sub_department_id.required' => __('Please select project'),
            'department_location_id.required' => __('Please select location'),

            'status_id.required' => __('The :attribute field is required', ['attribute' => __('status')]),
            'status_id.exists' => __('The selected :attribute is invalid', ['attribute' => __('status')]),

            'priority_id.required' => __('The :attribute field is required', ['attribute' => __('priority')]),
            'priority_id.exists' => __('The selected :attribute is invalid', ['attribute' => __('priority')]),

            'body.required' => __('The :attribute field is required', ['attribute' => __('body')]),
        ];
    }
}
