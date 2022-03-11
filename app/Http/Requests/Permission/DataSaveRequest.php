<?php

namespace App\Http\Requests\Permission;

use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use function App\Helpers\getParams;

class DataSaveRequest extends FormRequest
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
            'menuId' => 'required',
            'resource' => 'required|string',
            'dataMark' => 'required|string',
            'dataName' => 'required|string',
            'conditions' => 'required|string',
            'fields' => 'required|string',
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
