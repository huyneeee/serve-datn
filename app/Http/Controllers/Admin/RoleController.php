<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Traits\DeleteModelTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use DeleteModelTrait;
    private $role;
    private $permision;
    public function __construct(Role $role, Permission $permision)
    {
        $this->role = $role;
        $this->permision = $permision;
    }
    public function index(Request $request)
    {
        $pagesize = 10;
        $searchData = $request->except('page');
        if (count($request->all()) == 0) {
            // Lấy ra danh sách sản phẩm & phân trang cho nó
            $roles = $this->role->orderBy('id', 'desc')->paginate($pagesize);
        } else {
            $rolesQuery = $this->role->where('name', 'like', "%" . $request->keyword . "%");
            $roles = $rolesQuery->paginate($pagesize)->appends($searchData);
        }
        return response()->json($roles, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permisionsParent = $this->permision->where('parent_id', 0)->get();
        foreach ($permisionsParent as $item) {
            $permissionChildrent_id = $item->permissionChildrent;
        }
        return response()->json([
            'data' => $permisionsParent,
            // 'permissionChildrent_id' => $permissionChildrent_id,
        ], 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'display_name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        $role = $this->role->fill($request->all());
        $role->save();
        $role->permissions()->attach($request->permission_id);
        return response()->json([
            'data' => $role,
        ], 201);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role =  $this->role->find($id);
        return response()->json([
            'data' => $role,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = $this->role->find($id);
        $permissionsChecked = $role->permissions;
        // $permissionsChecked = $this->permision->where('parent_id', 0)->get();
        // foreach ($permissionsChecked as $item) {
        //     $permissionChildrent_id = $item->permissionChildrent;
        // }
        return response()->json([
            'data' => [
                'permissionsChecked' => $permissionsChecked,
                // 'permissionsChecked' => $permissionsChecked,
            ]
        ]);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'display_name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        $role = $this->role->find($id)->fill($request->all());
        $role->save();
        $role->permissions()->sync($request->permission_id);
        return response()->json([
            'data' => $role,
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
        $roles = $this->role->onlyTrashed()->paginate(5);
        return response()->json($roles, 200);
    }
    public function destroy($id)
    {
        //  $role_id = $this->role::find($id);
        //  $role_id->policies()->detach();
        return $this->deleteModelTrait($id, $this->role);
    }
    public function deleteChecked($id)
    {
        return $this->deleteCheckedModelTrait($id, $this->role);
    }
    public function restore($id)
    {
        return $this->restoreModelTrait($id, $this->role);
    }
    public function restoreAll()
    {
        return $this->restoreAllModelTrait($this->role);
    }
}
