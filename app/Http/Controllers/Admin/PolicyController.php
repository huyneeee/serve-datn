<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use App\Traits\DeleteModelTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use DeleteModelTrait;
    private $policy;
    public function __construct(Policy $policy)
    {
        $this->policy = $policy;
    }
    public function index(Request $request)
    {
        $pagesize = 10;
        $searchData = $request->except('page');
        if (count($request->all()) == 0) {
            // Lấy ra danh sách sản phẩm & phân trang cho nó
            $policies = $this->policy->orderBy('id', 'desc')->paginate($pagesize);
        } else {
            $policiesQuery = $this->policy->where('name', 'like', "%" . $request->keyword . "%");
            $policies = $policiesQuery->paginate($pagesize)->appends($searchData);
        }
        return response()->json($policies, 200);
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
            'name' => 'required',
            'detail' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }
        $policy = $this->policy->fill($request->all());
        $policy->save();
        return response()->json([
            'code' => 201,
            'data' => $policy,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $policy_id =  $this->policy->find($id);
        return response()->json([
            'code' => 200,
            'data' => $policy_id,
        ]);
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
            'name' => 'required',
            'detail' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }
        $policy = $this->policy->find($id)->fill($request->all());
        $policy->save();
        return response()->json([
            'code' => 200,
            'data' => $policy,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function viewDelete(Request $request)
    {
        $policies = $this->policy->onlyTrashed()->paginate(5);
        return response()->json($policies, 200);
    }
    public function destroy($id)
    {
        return $this->deleteModelTrait($id, $this->policy);
    }
    public function forceDelete($id)
    {
        return $this->forceDeleteModelTrait($id, $this->policy);
    }
    public function deleteChecked($id)
    {
        return $this->deleteCheckedModelTrait($id, $this->policy);
    }
    public function restore($id)
    {
        return $this->restoreModelTrait($id, $this->policy);
    }
    public function restoreAll()
    {
        return $this->restoreAllModelTrait($this->policy);
    }
}
