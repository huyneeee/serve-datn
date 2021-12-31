<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Traits\DeleteModelTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use DeleteModelTrait;
    private $customer;
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }
    public function index(Request $request)
    {
        $pagesize = 10;
        $searchData = $request->except('page');

        if (count($request->all()) == 0) {
            // Lấy ra danh sách sản phẩm & phân trang cho nó
            $customers = $this->customer->orderBy('id', 'desc')->paginate($pagesize);
        } else {
            $customersQuery = $this->customer->where('first_name', 'like', "%" . $request->keyword . "%");
            $customers = $customersQuery->orderBy('id', 'desc')->paginate($pagesize)->appends($searchData);
        }
        return response()->json($customers, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $show_customer =  $this->customer->find($id);
        return response()->json([
            'data' => $show_customer,
        ], 200);
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
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function viewDelete(Request $request)
    {
        $view_delete = $this->customer->onlyTrashed()->paginate(5);
        return response()->json($view_delete, 200);
    }
    public function forceDelete($id)
    {
        return $this->forceDeleteModelTrait($id, $this->customer);
    }
    public function destroy($id)
    {
        return $this->deleteModelTrait($id, $this->customer);
    }
    public function deleteChecked($id)
    {
        return $this->deleteCheckedModelTrait($id, $this->customer);
    }
    public function restore($id)
    {
        return $this->restoreModelTrait($id, $this->customer);
    }
    public function restoreAll()
    {
        return $this->restoreAllModelTrait($this->customer);
    }
}
