<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Departure;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = User::count();
        $car = Car::count();
        $departure = Departure::count();
        $customer = Customer::count();
        $total_price_thanh_toan = Payment::sum('price');
        $dadat = Invoice::count();
        $dathanhtoan = Payment::where('vnp_response_code', '=', '00')->count();
        $chuathanhtoan = Payment::where('vnp_response_code', '<>', '00')->count();
        //  dd($dadat);
        return response()->json([
            'code' => 200,
            'data' => [
                'user' => $user,
                'car' => $car,
                'departure' => $departure,
                'customer' => $customer,
                'total_price_thanh_toan' => $total_price_thanh_toan,
                'dadat' => $dadat,
                'dathanhtoan' => $dathanhtoan,
                'chuathanhtoan' => $chuathanhtoan,
            ],
        ]);
    }
    public function date_from(Request $request)
    {
        $invoice = Payment::all();
        if ($request->date_from && $request->date_to) {
            $invoice = Payment::whereBetween('time', [$request->date_from, $request->date_to])->sum('price');
        }
        return response()->json([
            'code' => 200,
            'data' => $invoice
        ]);
    }
    public function date_month(Request $request)
    {
        // $today = Carbon::now('Asia/Ho_Chi_Minh')->format('d-m-Y H:i:s');
        $dauthangnay = Carbon::now('Asia/Ho_Chi_Minh')->startOfMonth()->toDateString();
        $dauthangtruoc = Carbon::now('Asia/Ho_Chi_Minh')->subMonth()->startOfMonth()->toDateString();
        $cuoithangtruoc = Carbon::now('Asia/Ho_Chi_Minh')->subMonth()->endOfMonth()->toDateString();
        $seven_date = Carbon::now('Asia/Ho_Chi_Minh')->subDays(7)->toDateString();
        $date_365 = Carbon::now('Asia/Ho_Chi_Minh')->subDays(365)->toDateString();
        $now = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();
        if ($request->seven_date == "7ngay") {
            $invoice = Payment::where('vnp_response_code', '=', '00')->whereBetween('time', [$seven_date, $now])->sum('price');
        } elseif ($request->thangtruoc == "thangtruoc") {
            $invoice = Payment::where('vnp_response_code', '=', '00')->whereBetween('time', [$dauthangtruoc, $cuoithangtruoc])->sum('price');
        } elseif ($request->thangnay == "thangnay") {
            $invoice = Payment::where('vnp_response_code', '=', '00')->whereBetween('time', [$dauthangtruoc, $now])->sum('price');
        } else {
            $invoice = Payment::where('vnp_response_code', '=', '00')->whereBetween('time', [$date_365, $now])->sum('price');
        }
        // foreach ($invoice as $key => $value) {
        //     $invoice_data[] = array(
        //         'price' => $value->price,
        //         'note' => $value->note,
        //         'code_bank' => $value->code_bank,
        //     );
        // }
        // $data = json_encode($invoice_data);
        return response()->json([
            'code' => 200,
            'data' => $invoice
        ]);
    }
}
