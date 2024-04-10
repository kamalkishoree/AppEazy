<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
Use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Auth;
class OrderDriverRatingRequest extends FormRequest{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(){
        $id = Auth::id();
        return [
           // 'rating' => 'required_without_all:rating_types,rating_types_Coming',
            'review' => 'max:500',            
            'order_id' => 'required|exists:orders,id',                        
        ];
    }
    public function messages(){
        return [
            'order_vendor_product_id.required' => 'Required Vendor Product',
            'order_vendor_product_id.exists' =>  'Invalid Vendor Product',
            'order_id.required' => 'Required order',
            'order_id.exists' =>  'Invalid order',
            'product_id.required' => 'Required Product'
            ];
    }

    /**
     * [failedValidation [Overriding the event validator for custom error response]]
     * @param  Validator $validator [description]
     * @return [object][object of various validation errors]
     */
    public function failedValidation(Validator $validator)
    {
        $data_error = [];
        $error = $validator->errors()->all(); #if validation fail print error messages
        foreach ($error as $key => $errors):
            $data_error['status'] = 400;
            $data_error['message'] = $errors;
        endforeach;
        //write your bussiness logic here otherwise it will give same old JSON response
        throw new HttpResponseException(response()->json($data_error));

    }
}
