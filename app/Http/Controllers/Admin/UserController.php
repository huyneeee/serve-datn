<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Traits\StorageImageTrait;
use Illuminate\Http\Request;
use App\Traits\DeleteModelTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use StorageImageTrait;
    use DeleteModelTrait;
    private $user;
    private $role;
    public function __construct(User $user, Role $role)
    {
        $this->user = $user;
        $this->role = $role;
    }
    public function index(Request $request)
    {
        $pagesize = 10;
        $searchData = $request->except('page');

        if (count($request->all()) == 0) {
            // Lấy ra danh sách sản phẩm & phân trang cho nó
            $users = $this->user->orderBy('id', 'desc')->paginate($pagesize);
            foreach ($users as $item) {
                $users_id = $item->roles;
            }
        } else {
            $usersQuery = $this->user->where('name', 'like', "%" . $request->keyword . "%")->orderBy('id', 'desc');
            $users = $usersQuery->paginate($pagesize)
                ->appends($searchData);
            foreach ($users as $item) {
                $users_id = $item->roles;
            }
        }
        return response()->json([
            'data' => $users,
            'searchData' => $searchData,
            'code' => '200',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewDelete(Request $request)
    {
        $users = $this->user->onlyTrashed()->paginate(5);
        foreach ($users as $item) {
            $users_id = $item->roles;
        }
        return response()->json($users, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100|min:6',
            'email' => 'required|email|unique:users,email',
            'address' => 'required',
            // 'image' => 'mimes:jpg,bmp,png,jpeg|required',
            // 'image_degree' => 'mimes:jpg,bmp,png,jpeg',
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
            // if ($request->hasFile('image')) {
            //     $imagePath = $request->file('image')->storeAs('public/users', uniqid() . '-' . $request->image->getClientOriginalName());
            //     $dataInssert->image =  Storage::url($imagePath);
            // }
            // if ($request->hasFile('image_degree')) {
            //     $image_degreePath = $request->file('image_degree')->storeAs('public/users', uniqid() . '-' . $request->image_degree->getClientOriginalName());
            //     $dataInssert->image_degree =  Storage::url($image_degreePath);
            // }
            $dataInssert->save();
            $dataInssert->roles()->attach($request->role_id);
            $token = $dataInssert->createToken('api_authUser')->plainTextToken;
            DB::commit();
            return response()->json([
                'code' => 201,
                'token' => $token,
                'data' => $dataInssert,
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("Message :" . $exception->getMessage() . '---Line:' . $exception->getLine());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user =  $this->user->find($id);
        foreach ($user->roles as $item) {
        }
        return response()->json([
            'data' => $user,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_role(Request $request, $id)
    {
        $dataUpdate = $this->user->find($id);
        $dataUpdate->fill($request->all());
        $dataUpdate->save();
        $dataUpdate->roles()->sync($request->role_id);
        return response()->json([
            'data' => $dataUpdate,
        ], 200);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100|min:6',
            'email' => [
                'required',
                Rule::unique('users')->ignore($request->id),
                'email',
            ],
            'address' => 'required',
            // 'image' => 'mimes:jpg,bmp,png,jpeg|required',
            'password' => 'required|min:6|max:40',
            'passwordAgain' => 'required|same:password',
            'phone' => 'required|numeric|digits_between:10,11',
            'role_id' => 'required',
            // 'image_degree' => 'mimes:jpg,bmp,png,jpeg',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        try {
            DB::beginTransaction();
            $dataUpdate = $this->user->find($id);
            $dataUpdate->fill($request->all());
            $dataUpdate->password = bcrypt($request->password);
            // if ($request->hasFile('image')) {
            //     $imagePath = $request->file('image')->storeAs('public/users', uniqid() . '-' . $request->image->getClientOriginalName());
            //     $dataUpdate->image =  Storage::url($imagePath);
            // }
            // if ($request->hasFile('image_degree')) {
            //     $image_degreePath = $request->file('image_degree')->storeAs('public/users', uniqid() . '-' . $request->image_degree->getClientOriginalName());
            //     $dataUpdate->image_degree =  Storage::url($image_degreePath);
            // }
            $dataUpdate->save();
            $dataUpdate->roles()->sync($request->role_id);
            DB::commit();
            return response()->json([
                'data' => $dataUpdate,
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("Message :" . $exception->getMessage() . '---Line:' . $exception->getLine());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forceDelete($id)
    {
        $user_id = $this->user::withTrashed()->find($id);
        $user_id->roles()->detach();
        return $this->forceDeleteModelTrait($id, $this->user);
    }
    public function destroy($id)
    {
        return $this->deleteModelTrait($id, $this->user);
    }
    public function deleteChecked($id)
    {
        return $this->deleteCheckedModelTrait($id, $this->user);
    }
    public function restore($id)
    {
        return $this->restoreModelTrait($id, $this->user);
    }
    public function restoreAll()
    {
        return $this->restoreAllModelTrait($this->user);
    }
}
