<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Departure;
use App\Models\Invoice;
use App\Traits\DeleteModelTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    use DeleteModelTrait;
    private $invoice;
    private $departure;
    public function __construct(Invoice $invoice, Departure $departure)
    {
        $this->invoice = $invoice;
        $this->departure = $departure;
    }
    public function index(Request $request)
    {
        $pagesize = 10;
        $searchData = $request->except('page');
        if (count($request->all()) == 0) {
            // Lấy ra danh sách sản phẩm & phân trang cho nó
            $invoices = $this->invoice->orderBy('id', 'desc')->paginate($pagesize);
            $invoices->load('customer');
            $invoices->load('departure');
        } else {
            $invoicesQuery =  $this->invoice::where('phone', 'LIKE', "%" . $request->phone . "%");
            $invoices = $invoicesQuery->orderBy('id', 'desc')->paginate($pagesize)->appends($searchData);
            $invoices->load('customer');
            $invoices->load('departure');
        }
        return response()->json($invoices, 200);
    }
    public function invoice_detail($id)
    {
        $invoice_detail =  $this->invoice::find($id);
        $invoice_detail->load('customer');
        $invoice_detail->load('departure');
        $invoice_detail->load('payment_invoice');
        return response()->json([
            'data' => $invoice_detail,
        ], 200);
    }
    public function update_invoice(Request $request, $id)
    {
        $dataUpdate = $this->invoice->find($id);
        $dataUpdate->fill($request->all());
        $dataUpdate->save();
        return response()->json([
            'data' => $dataUpdate,
        ], 201);
    }
    //update chuyến 
    public function updateDeparture(Request $request, $id)
    {
        $model = $this->departure->find($id);
        $model->fill($request->all());
        $model->save();
        return response()->json([
            'data' => $model,
        ], 201);
    }
    //list chuyến theo điều kiện
    public function whereDeparture(Request $request)
    {
        $pagesize = 5;
        $searchData = $request->except('page');
        $ten_minutes = Carbon::now('Asia/Ho_Chi_Minh')->addHours(1)->toDateTimeString();
        $departureWhereTime = $this->departure->where('start_time', '>', $ten_minutes);
        $departureWhere = $departureWhereTime->where('seats_departures', '<>', 0);
        if ($departureWhere == true) {
            if ($request->has('go_location_city') && $request->go_location_city != "") {
                $departureWhere = $departureWhere->where('go_location_city', 'like', "%" . $request->go_location_city . "%");
            }
            if ($request->has('come_location_city') && $request->come_location_city != "") {
                $departureWhere = $departureWhere->where('come_location_city', 'like', "%" . $request->come_location_city . "%");
            }
            if ($request->has('start_time') && $request->start_time != "") {
                $departureWhere = $departureWhere->whereBetween('start_time', [$request->start_time, date("Y-m-d", strtotime('+7 day', strtotime($request->start_time)))]);
            }
            if ($request->has('name') && $request->name != "") {
                $departureWhere = $departureWhere->where('name', 'like', "%" . $request->name . "%");
            }
            if ($request->price_form && $request->price_to != "") {
                $departureWhere = $departureWhere->whereBetween('price', [$request->price_form, $request->price_to]);
            }

            $departures = $departureWhere->orderBy('start_time', 'asc')->paginate($pagesize)->appends($searchData);
            $departures->load('car_departure');
            $departures->load('user_departure');

            return response()->json($departures, 200);
        }
    }
    public function viewDelete(Request $request)
    {
        $viewDelete = $this->invoice->onlyTrashed()->paginate(5);
        $viewDelete->load('customer');
        return response()->json($viewDelete, 200);
    }
    public function destroy($id)
    {
        return $this->deleteModelTrait($id, $this->invoice);
    }
    public function forceDelete($id)
    {
        return $this->forceDeleteModelTrait($id, $this->invoice);
    }
    public function deleteChecked($id)
    {
        return $this->deleteCheckedModelTrait($id, $this->invoice);
    }
    public function restore($id)
    {
        return $this->restoreModelTrait($id, $this->invoice);
    }
    public function restoreAll()
    {
        return $this->restoreAllModelTrait($this->invoice);
    }
}
