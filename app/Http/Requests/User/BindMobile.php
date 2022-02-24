<?php

namespace App\Http\Requests\User;

use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BindMobile extends FormRequest
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
            'mobile' => 'required',
            'code' => 'required|size:4',
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.required' => '请输入正确的手机号',
            'code.size' => '请输入正确的验证码！',
        ];
    }

    /**
     * @param Validator $validator
     * @throws CommonException
     */
    public function failedValidation(Validator $validator): void
    {
        throw new CommonException(
            ErrorCode::INVALID_ARGUMENT,
            $validator->errors()->first()
        );
    }
}
