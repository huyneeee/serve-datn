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
            if ($request->has('id_departure') && $request->id_departure != "") {
                $departureWhere = $departureWhere->where('id', '<>', $request->id_departure);
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
    //xác nhận theo chuyến
    public function departureConfirmed(Request $request, $id)
    {
        $pagesize = 10;
        $searchData = $request->except('page');
        $departure_confirmed =  $this->departure::find($id)->invoice_departure()->where('status', '=', 1)->orderBy('created_at', 'desc')->paginate($pagesize)->appends($searchData);
        $departure_confirmed->load('customer');
        $departure_confirmed->load('payment_invoice');
        return response()->json($departure_confirmed, 200);
    }
    //chưa xác nhận theo chuyến
    public function departureUnconfimred(Request $request, $id)
    {
        $pagesize = 10;
        $searchData = $request->except('page');
        $departure_unconfimred =  $this->departure::find($id)->invoice_departure()->where('status', '=', 0)->orderBy('created_at', 'desc')->paginate($pagesize)->appends($searchData);
        $departure_unconfimred->load('customer');
        $departure_unconfimred->load('payment_invoice');
        return response()->json($departure_unconfimred, 200);
    }
    //hủy vé theo chuyến
    public function viewDeleteDeparture(Request $request, $id)
    {
        $pagesize = 10;
        $searchData = $request->except('page');
        $viewDelete = $this->departure::find($id)->invoice_departure()->onlyTrashed()->orderBy('created_at', 'desc')->paginate($pagesize)->appends($searchData);
        $viewDelete->load('customer');
        $viewDelete->load('payment_invoice');
        return response()->json($viewDelete, 200);
    }
    //count vé theo chuyến
    public function countStatusDeparture(Request $request, $id)
    {
        $departure_unconfimred =  $this->departure::find($id)->invoice_departure()->where('status', '=', 0)->count();
        $departure_confirmed =  $this->departure::find($id)->invoice_departure()->where('status', '=', 1)->count();
        $viewDelete = $this->departure::find($id)->invoice_departure()->onlyTrashed()->count();
        return response()->json([
            'data' => [
                'departure_unconfimred' => $departure_unconfimred,
                'departure_confirmed' => $departure_confirmed,
                'viewDelete' => $viewDelete
            ]
        ], 200);
    }
    //vé đã được xác nhận
    public function Confirmed(Request $request)
    {
        $confirmed_invoice = $this->invoice->where('status', '=', 1)->orderBy('id', 'desc')->paginate(10);
        $confirmed_invoice->load('departure');
        $confirmed_invoice->load('payment_invoice');
        return response()->json($confirmed_invoice, 200);
    }
    //vé chưa xác nhận
    public function Unconfimred(Request $request)
    {
        $unconfimred_invoice = $this->invoice->where('status', '=', 0)->orderBy('id', 'desc')->paginate(10);
        $unconfimred_invoice->load('departure');
        $unconfimred_invoice->load('payment_invoice');
        return response()->json($unconfimred_invoice, 200);
    }
    //count trạng thái của vé
    public function countStatus(Request $request)
    {
        $confirmed_invoice = $this->invoice->where('status', '=', 1)->count();
        $unconfimred_invoice = $this->invoice->where('status', '=', 0)->count();
        $viewDelete = $this->invoice->onlyTrashed()->count();
        return response()->json([
            'data' => [
                'confirmed_invoice' => $confirmed_invoice,
                'unconfimred_invoice' => $unconfimred_invoice,
                'viewDelete' => $viewDelete
            ]
        ], 200);
    }
    public function viewDelete(Request $request)
    {
        $viewDelete = $this->invoice->onlyTrashed()->paginate(5);
        foreach ($viewDelete as $item) {
            $item->load('customer');
            $item->load('departure');
            $item->load('payment_invoice');
        }
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
