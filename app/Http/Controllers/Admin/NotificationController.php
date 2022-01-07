<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Role;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $notification;
    private $role;
    public function __construct(Notification $notification, Role $role)
    {
        $this->notification = $notification;
        $this->role = $role;
    }
    public function index()
    {
        $notification = $this->notification->all();
        return response()->json([
            'data' => $notification,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $notification = $this->notification->fill($request->all());
        $notification->save();
        return response()->json([
            'data' => $notification,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function usersRole(Request $request, $id)
    {
        $pagesize = 10;
        $searchData = $request->except('page');
        $usersRole_id = $this->role->find($id)->notification_role()->where('is_send', '=', 1)->orderBy('created_at', 'desc')->paginate($pagesize)->appends($searchData);
        return response()->json($usersRole_id, 200);
    }

    //đếm thông báo của user
    public function usersRoleCount(Request $request, $id)
    {
        $usersRole_id = $this->role->find($id)->notification_role()->where('is_send', '=', 1)->get();
        $usersRole_count =   $usersRole_id->where('status', '=', 0)->count();
        return response()->json([
            'data' => $usersRole_count
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
        $notification_id = $this->notification->find($id);
        $notification_id->fill($request->all());
        $notification_id->save();
        return response()->json([
            'data' => $notification_id,
        ], 201);
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
