<?php

namespace App\Http\Requests\Message;

use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use function App\Helpers\getParams;

class SaveRequest extends FormRequest
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
            'title' => 'required|string',
            'catId' => 'required',
            'text' => 'required|string',
            'publisher' => 'required|string',
        ];
    }
    
    /**
     * @return array|void
     */
    public function validationData()
    {
        $request = $this->all();
        return getParams($request);
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
