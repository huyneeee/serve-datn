<?php

namespace App\Http\Controllers\Client;


use App\Http\Controllers\Controller;
use App\Http\Requests\CustommerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Traits\DeleteModelTrait;
use App\Traits\StorageImageTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CustommerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use StorageImageTrait;
    use DeleteModelTrait;
    private $customer;
    public function __construct(Customer $customer)
    {
        // $this->middleware('auth:customer');
        $this->customer = $customer;
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email',
            'password' => 'min:6|max:40',
            'phone_number' => 'numeric|digits_between:10,11'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        $customerlogin = Customer::where('email', $request->email)
            ->orWhere('phone_number', $request->phone_number)->first();
        // dd($customerlogin);
        if ($customerlogin->status != 0) {
            if (
                Auth::guard('customer')->attempt(['email' => $request->email, 'password' => $request->password]) ||
                Auth::guard('customer')->attempt(['phone_number' => $request->phone_number, 'password' => $request->password])
            ) {
                $token = $customerlogin->createToken('api_authcustomer')->plainTextToken;
                // dd($token);
                return response()->json([
                    'code' => '200',
                    'data' => [
                        'token' => $token,
                        'customerlogin' => $customerlogin,
                    ],
                    "message" => "Đăng Nhập Thành Công",
                ]);
            } else {
                return response()->json([
                    "code" => 401,
                    "message" => "Tài khoản hoặc mật khẩu không đúng",
                ], 401);
            }
        } else {
            return response()->json([
                "code" => 401,
                "message" => "Tài Khoản Đã Bị Khóa",
            ], 401);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'last_name' => 'required|max:100',
            'first_name' => 'required|max:100',
            'email' => [
                'required',
                Rule::unique('customers')->ignore($this->customer->id),
                'email',
            ],
            'image' => 'mimes:jpg,bmp,png,jpeg',
            'status' => 'boolean',
            'password' => 'required|min:6|max:40',
            'passwordAgain' => 'required|same:password',
            'phone_number' => [
                'required',
                Rule::unique('customers')->ignore($this->customer->id),
                'numeric',
                'digits_between:10,11',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        $model = $this->customer;
        $model->fill($request->all());
        $model->password = bcrypt($request->password);
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->storeAs('public/customers', uniqid() . '-' . $request->image->getClientOriginalName());
            $model->image =  Storage::url($imagePath);
        }
        $model->save();
        $token = $model->createToken('api_authcustomer')->plainTextToken;
        return response()->json([
            'data' => [
                'token' => $token,
                'model' => $model,
            ],
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => "Logout Thành Công",
        ]);
    }
    public function show($id)
    {
        $customer = $this->customer::find($id);
        return response()->json([
            'data' => $customer,
            'code' => 200
        ]);
    }

    public function updateLogin(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'last_name' => 'max:100',
            'first_name' => 'max:100',
            'email' => [
                Rule::unique('customers')->ignore($request->id),
                'email',
            ],
            'image' => 'mimes:jpg,bmp,png,jpeg',
            'status' => 'boolean',
            'password' => 'min:6|max:40',
            'passwordAgain' => 'same:password',
            'phone_number' => [
                '',
                Rule::unique('customers')->ignore($request->id),
                'numeric',
                'digits_between:10,11',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        $model = $this->customer->find($id);
        $model->fill($request->all());
        //    $model->password = bcrypt($request->password);
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->storeAs('public/customers', uniqid() . '-' . $request->image->getClientOriginalName());
            $model->image =  Storage::url($imagePath);
        }
        $model->save();
        $token = $model->createToken('api_authcustomer')->plainTextToken;
        return response()->json([
            'code' => 201,
            'data' => [
                'token' => $token,
                'model' => $model,
            ],
        ], 201);
    }

    public function updatePassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'last_name' => 'max:100',
            'first_name' => 'max:100',
            'email' => [
                Rule::unique('customers')->ignore($request->id),
                'email',
            ],
            'image' => 'mimes:jpg,bmp,png,jpeg',
            'status' => 'boolean',
            'newPassword' => 'required|min:6|max:40',
            'phone_number' => [
                '',
                Rule::unique('customers')->ignore($request->id),
                'numeric',
                'digits_between:10,11',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        $customer_id = $this->customer->find($id)->password;
        // $requestPassword = password_verify($request->password, $customer_id);
        // dd($requestPassword);
        if (password_verify($request->oldPassword, $customer_id) == true) {
            $model = $this->customer->find($id);
            $model->fill($request->all());
            $model->password = bcrypt($request->newPassword);
            $model->save();
            $token = $model->createToken('api_authcustomer')->plainTextToken;
            return response()->json([
                'message' => 'Bạn Đã Đổi Mật Khẩu Thành Công',
                'data' => [
                    'token' => $token,
                    'model' => $model,
                ],
            ], 201);
        }
        return response()->json([
            'message' => 'Bạn Nhập Mật Khẩu Không Đúng',
        ], 400);
    }
}
