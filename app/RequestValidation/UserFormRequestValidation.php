<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class UserFormRequestValidation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'college_name' =>'required|min:2|max:255',
            'ai_code' =>'required|numeric',
            'ssoid' => 'required|unique:users|min:2|max:255'
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
            'college_name.required' => 'The :attribute field himatcan not be blanksssss value',
            'ai_code.required' => 'The :attribute field rahul can not be blanksssss value',
            'ssoid.required' => 'The :attribute field rohit can not be blanksssss value',
        ];
    }
}