<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Departure;
use App\Models\Invoice;
use App\Traits\DeleteModelTrait;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    use DeleteModelTrait;
    private $invoice;
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
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
