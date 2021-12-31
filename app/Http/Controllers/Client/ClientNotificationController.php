<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Notification;
use Illuminate\Http\Request;

class ClientNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $notification;
    private $customer;
    public function __construct(Notification $notification, Customer $customer)
    {
        $this->notification = $notification;
        $this->customer = $customer;
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
    public function customer(Request $request, $id)
    {
        $pagesize = 10;
        $searchData = $request->except('page');
        $customer_id = $this->customer->find($id)->notifications()->where('is_send', '=', 1)->orderBy('created_at', 'desc')->paginate($pagesize)->appends($searchData);
        return response()->json($customer_id, 200);
    }
    //đếm thông báo của customer
    public function customerCount(Request $request, $id)
    {
        $customer_is_send = $this->customer->find($id)->notifications()->where('is_send', '=', 1)->get();
        $customer_count =   $customer_is_send->where('status', '=', 0)->count();
        return response()->json([
            'data' => $customer_count
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
