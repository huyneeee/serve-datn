<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustommerRequest extends FormRequest
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
            'name' => 'required|max:100|min:6',
            'email' => [
                'required',
                Rule::unique('customers')->ignore($this->id),
                'email',
            ],
            'address' => 'required',
            'image' => 'mimes:jpg,bmp,png,jpeg|required',
            'status' => 'required',
            'password' => 'required|min:6|max:40',
            'passwordAgain' => 'required|same:password',
            'phone_number' => [
                'required',
                Rule::unique('customers')->ignore($this->id),
                'numeric',
                'digits_between:10,11',
            ],

        ];
    }
    public function messages()
    {
        return [
            'required' => ':attribute Không Được Bỏ Trống',
            'name.max' => 'Họ tên không được vượt quá 100 ký tự',
            'name.min' => 'Họ tên không được nhỏ quá 6 ký tự',
            'email.email' => 'Sai định dạnh email',
            'email.unique' => 'Email đã tồn tại',
            'phone_number.unique' => 'Số Điện Thoại đã tồn tại',
            'phone_number.digits_between' => 'Số Điện Thoại Phải Là Kiểu Số Không Được Thấp Hơn 10 Ký Tự Và Không Quá 11 Ký Tự',
            'image.mimes' => 'File Ảnh Sản Phẩm Không Đúng Định Dạng (jpg,bmp,png,jpeg)',
            'password.min' => 'Mật Khẩu Phải Có Ít Nhất 6 Ký Tự',
            'password.max' => 'Mật Khẩu Tối Đa 40 Ký Tự',
            'passwordAgain.same' => 'Mật Khẩu Nhập Lại Không Khớp Với Mật Khẩu Trên',
        ];
    }
    public function attributes()
    {
        return [
            'name' => 'Họ tên',
            'password' => 'Mật Khẩu',
            'passwordAgain' => 'Bạn Chưa Nhập Lại Mật Khẩu',
            'email' => 'Email',
            'address' => 'Địa chỉ',
            'image' => 'Ảnh',
            'status' => 'Tình Trạng',
            'phone_number' => 'Số Điện Thoại',
        ];
    }
}
