<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateCancellationRequest extends FormRequest
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
            'reason'        => 'required',
            'start_date'    => 'required',
            'end_date'      => 'required',
            'end_period'    => 'required',
            'end_period'    => 'required',
            'days'          => 'required',
            'working_days'  => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'reason.required'       => 'กรุณาระบุเหตุผลการยกเลิก',
            'start_date.required'    => 'กรุณาเลือกจากวันที่',
            'start_date.not_in'      => 'คุณมีการลาในวันที่ระบุแล้ว',
            'end_date.required'      => 'กรุณาเลือกถึงวันที่',
            'end_date.not_in'        => 'คุณมีการลาในวันที่ระบุแล้ว',
            'end_period.required'    => 'กรุณาเลือกช่วงเวลา',
            'days.required'          => '',
            'working_days.required'  => '',
        ];
    }
}
