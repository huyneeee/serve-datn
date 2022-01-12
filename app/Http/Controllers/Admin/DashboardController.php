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
        $invoice = Invoice::all();
        if ($request->date_from && $request->date_to) {
            $invoice = Invoice::whereBetween('date', [$request->date_from, $request->date_to])->sum('price');
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
            $invoice = Invoice::select('date', Invoice::raw('SUM(total_price) as total_price'), Invoice::raw('SUM(quantity) as quantity'))->where('status', '=', '1')->whereBetween('date', [$seven_date, $now])->groupBy('date')->get();
        } elseif ($request->thangtruoc == "thangtruoc") {
            $invoice = Invoice::select('date', Invoice::raw('SUM(total_price) as total_price'), Invoice::raw('SUM(quantity) as quantity'))->where('status', '=', '1')->whereBetween('date', [$dauthangtruoc, $cuoithangtruoc])->groupBy('date')->get();
        } elseif ($request->thangnay == "thangnay") {
            $invoice = Invoice::select('date', Invoice::raw('SUM(total_price) as total_price'), Invoice::raw('SUM(quantity) as quantity'))->where('status', '=', '1')->whereBetween('date', [$dauthangnay, $now])->groupBy('date')->get();
        } else {
            $invoice = Invoice::select('date', Invoice::raw('SUM(total_price) as total_price'), Invoice::raw('SUM(quantity) as quantity'))->where('status', '=', '1')->whereBetween('date', [$date_365, $now])->groupBy('date')->get();
        }
        return response()->json([
            'code' => 200,
            'data' => $invoice
        ]);
    }
    public function dateData(Request $request)
    {
        $column = Invoice::select('date', Invoice::raw('SUM(total_price) as total_price'), Invoice::raw('SUM(quantity) as quantity'))->where('status', '=', '1')->groupBy('date')->get();
        return response()->json([
            'code' => 200,
            'data' => $column
        ]);
    }
}
