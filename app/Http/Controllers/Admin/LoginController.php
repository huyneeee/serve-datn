<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\StorageImageTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use StorageImageTrait;
    private $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = $this->user::where('email', $request->email)->first();
            $token = $user->createToken('api_auth')->plainTextToken;
            return response()->json([
                'data' => [
                    'token' => $token,
                    'user' => $user,
                ],
                "message" => "Đăng Nhập Thành Công",
            ], 200);
        } else {
            return response()->json([
                "message" => "Tài Khoản Hoặc Mật Khẩu Không Đúng",
            ], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => "Logout Thành Công",
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100|min:6',
            'email' => 'required|email|unique:users,email',
            'address' => 'required',
            'image' => 'mimes:jpg,bmp,png,jpeg|required',
            'image_degree' => 'mimes:jpg,bmp,png,jpeg|required',
            'experience' => 'required',
            'password' => 'required|min:6|max:40',
            'passwordAgain' => 'required|same:password',
            'phone' => 'required|numeric|digits_between:10,11',
            'role_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        try {
            DB::beginTransaction();
            $dataInssert = $this->user;
            $dataInssert->fill($request->all());
            $dataInssert->password = bcrypt($request->password);
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->storeAs('public/users', uniqid() . '-' . $request->image->getClientOriginalName());
                $dataInssert->image =  Storage::url($imagePath);
            }
            if ($request->hasFile('image_degree')) {
                $image_degreePath = $request->file('image_degree')->storeAs('public/users', uniqid() . '-' . $request->image_degree->getClientOriginalName());
                $dataInssert->image_degree =  Storage::url($image_degreePath);
            }
            $dataInssert->save();
            $dataInssert->roles()->attach($request->role_id);
            $token = $dataInssert->createToken('api_authUser')->plainTextToken;
            DB::commit();
            return response()->json([
                'token' => $token,
                'data' => $dataInssert,
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("Message :" . $exception->getMessage() . '---Line:' . $exception->getLine());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
