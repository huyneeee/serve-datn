<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Departure;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\DeleteModelTrait;
use Illuminate\Support\Facades\Validator;

class DeparturesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use DeleteModelTrait;
    private $departure;
    private $user;
    private $car;
    public function __construct(Departure $departure, User $user, Car $car)
    {
        $this->departure = $departure;
        $this->user = $user;
        $this->car = $car;
    }
    public function index(Request $request)
    {
        $pagesize = 10;
        $searchData = $request->except('page');
        if (count($request->all()) == 0) {
            // Lấy ra danh sách sản phẩm & phân trang cho nó
            $departures = $this->departure->orderBy('id', 'desc')->paginate($pagesize);
            $departures->load('car_departure');
            $departures->load('user_departure');
            $departures->loadCount('invoice_departure');
        } else {
            $departuresQuery = $this->departure->where('name', 'like', "%" . $request->keyword . "%");
            if ($request->has('user_id') && $request->user_id != "") {
                $departuresQuery = $departuresQuery->where('user_id', $request->user_id);
            }
            if ($request->has('car_id') && $request->car_id != "") {
                $departuresQuery = $departuresQuery->where('car_id', $request->car_id);
            }
            if ($request->has('departure_code') && $request->departure_code != "") {
                $departuresQuery = $departuresQuery->where('departure_code', $request->departure_code);
            }

            $departures = $departuresQuery->orderBy('id', 'desc')->paginate($pagesize)->appends($searchData);
            $departures->load('car_departure');
            $departures->load('user_departure');
            $departures->loadCount('invoice_departure');
        }
        return response()->json([
            'data' => $departures,
            'searchData' => $searchData,
        ], 200);
    }
    public function departure_invoice($id)
    {
        $invoice_departure =  $this->departure::find($id);
        foreach ($invoice_departure->invoice_departure as $item) {
            $item->load('customer');
            $item->load('payment_invoice');
        }
        return response()->json([
            'data' => $invoice_departure,
        ], 200);
    }
    public function getDataRoleCar()
    {
        $user_drive = $this->user::whereHas('roles', function ($query) {
            $drive =   $query->where('name', 'drive');
            $drive->where('status_drive', '=', '0');
        })->get();
        $all_car = $this->car::where('status', '=', 0)->get();
        return response()->json([
            'user_drive' => $user_drive,
            'all_car' => $all_car,
        ], 200);
    }
    //update user role
    public function updateUserDrive(Request $request, $id)
    {
        $model = $this->user->find($id);
        $model->fill($request->all());
        $model->save();
        return response()->json([
            'data' => $model,
        ], 200);
    }
    //update xe
    public function updateCar(Request $request, $id)
    {
        $model = $this->car->find($id);
        $model->fill($request->all());
        $model->save();
        return response()->json([
            'data' => $model,
        ], 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function randomCode($lenght = 6)
    {
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $codeAlphabetLenght = strlen($codeAlphabet);
        $randomString = '';
        for ($i = 0; $i < $lenght; $i++) {
            $randomString .= $codeAlphabet[rand(0, $codeAlphabetLenght - 1)];
        }
        return $randomString;
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'user_id' => 'required',
            'car_id' => 'required',
            'price' => 'required',
            'go_location_city' => 'required',
            'go_location_district' => 'required',
            'go_location_wards' => 'required',
            'come_location_city' => 'required',
            'come_location_district' => 'required',
            'come_location_wards' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'seats_departures' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        $dataInssert = $this->departure;
        $dataInssert->fill($request->all());
        $dataInssert->departure_code = $this->randomCode();
        $dataInssert->save();
        return response()->json([
            'data' => $dataInssert,
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
        $departure_id =  $this->departure->find($id);
        $departure_id->load('car_departure');
        $departure_id->load('user_departure');
        return response()->json([
            'data' => $departure_id,
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
            'name' => 'required',
            'user_id' => 'required',
            'car_id' => 'required',
            'price' => 'required',
            'go_location_city' => 'required',
            'go_location_district' => 'required',
            'go_location_wards' => 'required',
            'come_location_city' => 'required',
            'come_location_district' => 'required',
            'come_location_wards' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'seats_departures' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        $dataInssert = $this->departure->find($id);
        $dataInssert->fill($request->all());
        $dataInssert->save();
        return response()->json([
            'data' => $dataInssert,
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
        $departures = $this->departure->onlyTrashed()->paginate(5);
        return response()->json($departures, 200);
    }
    public function destroy($id)
    {
        return $this->deleteModelTrait($id, $this->departure);
    }
    public function forceDelete($id)
    {
        return $this->forceDeleteModelTrait($id, $this->departure);
    }
    public function deleteChecked($id)
    {
        return $this->deleteCheckedModelTrait($id, $this->departure);
    }
    public function restore($id)
    {
        return $this->restoreModelTrait($id, $this->departure);
    }
    public function restoreAll()
    {
        return $this->restoreAllModelTrait($this->departure);
    }
}
