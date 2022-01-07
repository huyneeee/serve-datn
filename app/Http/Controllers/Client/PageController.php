<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Departure;
use App\Models\Invoice;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $invoice;
    private $departure;
    private $car;
    private $customer;
    private $news;
    public function __construct(Invoice $invoice, Departure $departure, Car $car, Customer $customer, News $news)
    {
        $this->invoice = $invoice;
        $this->departure = $departure;
        $this->car = $car;
        $this->customer = $customer;
        $this->news = $news;
    }

    // public function departureAll(Request $request)
    // {
    //     $pagesize = 10;
    //     $searchData = $request->except('page');
    //     if (count($request->all()) == 0) {
    //         // Lấy ra danh sách sản phẩm & phân trang cho nó
    //         $departures = $this->departure->orderBy('id', 'desc')->paginate($pagesize);
    //         // $departures = $this->departure::with(['comment_departure' => function ($query) {
    //         //     $query->where('status', '<>', 0);
    //         // }])->get();
    //         $departures->load('car_departure');
    //         $departures->load('user_departure');
    //         $departures->load('car_images');
    //         $departures->load('policies');
    //     } else {
    //         $departuresQuery = $this->departure->where('name', 'like', "%" . $request->keyword . "%");
    //         if ($request->has('price') && $request->price != "") {
    //             $departuresQuery = $departuresQuery->where('price', $request->price);
    //         }
    //         $departures = $departuresQuery->orderBy('id', 'desc')->paginate($pagesize)->appends($searchData);
    //         // $departures = $this->departure::with(['comment_departure' => function ($query) {
    //         //     $query->where('status', '<>', 0);
    //         // }])->get();
    //         $departures->load('car_departure');
    //         $departures->load('user_departure');
    //         $departures->load('car_images');
    //         $departures->load('policies');
    //     }
    //     return response()->json($departures, 201);
    // }
    //xuất chuyến theo bộ lọc
    public function departureFilter(Request $request)
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
    public function carDeparture(Request $request, $id)
    {
        $car_id = $this->car->find($id);
        $car_id->load('car_images');
        $car_id->load('policies');
        return response()->json([
            'data' => $car_id,
        ], 200);
    }
    //Bình luận chuyến
    public function view_comment_departure(Request $request, $id)
    {
        $pagesize = 10;
        $comment_departure_id =  $this->departure::find($id)->comment_departure()->where('status', '<>', 0)->paginate($pagesize);
        $comment_departure_id->load('customer');
        return response()->json($comment_departure_id, 200);
    }
    //
    public function randomCode($lenght = 6)
    {
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabetLenght = strlen($codeAlphabet);
        $randomString = '';
        for ($i = 0; $i < $lenght; $i++) {
            $randomString .= $codeAlphabet[rand(0, $codeAlphabetLenght - 1)];
        }
        return $randomString;
    }

    //Mua vé 
    public function addInvoice($id, Request $request)
    {
        $idDeparture = $id;
        $departure = $this->departure::find($id);
        $invoice = $this->invoice;
        $invoice->departure_id = $idDeparture;
        $invoice->customers_id = Auth::user()->id;
        $invoice->phone = $request->phone;
        $invoice->note = $request->note;
        $invoice->name = $request->name;
        $invoice->email = $request->email;
        $invoice->go_point = $request->go_point;
        $invoice->come_point = $request->come_point;
        $invoice->quantity = $request->quantity;
        $invoice->form_payment = $request->form_payment;
        $invoice->total_price = $request->total_price;
        $invoice->status = $request->status;
        $invoice->invoice_code = $this->randomCode();
        $invoice->date = Carbon::now();
        $invoice->save();
        return response()->json([
            'data' => $invoice,
            'code' => 201,
        ], 201);
    }
    //search vé
    public function invoiceCodeFilter(Request $request)
    {
        if ($request->has('invoice_code') && $request->invoice_code != "") {
            $invoiceFilter = $this->invoice->where('invoice_code', $request->invoice_code)->get();
        }
        $invoiceFilter->load('customer');
        $invoiceFilter->load('departure');
        $invoiceFilter->load('payment_invoice');
        return response()->json([
            'data' => $invoiceFilter,
            'code' => 200,
        ], 200);
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
    //lịch sử đặt chuyến
    public function historyInvoice(Request $request)
    {
        $pagesize = 10;
        $searchData = $request->except('page');
        $customer_id = $request->user()->customer_invoice()->orderBy('created_at', 'desc')->paginate($pagesize)->appends($searchData);
        foreach ($customer_id as $item) {
            $item->load('departure');
            $item->load('payment_invoice');
        }
        return response()->json([
            'data' => $customer_id,
        ], 200);
    }

    //list tin tức
    public function newList(Request $request)
    {
        $news = $this->news->orderBy('id', 'desc')->paginate(10);
        return response()->json($news, 200);
    }
    public function newDetail($slug)
    {
        $new_detail = $this->news->where('slug', '=', $slug)->first();
        return response()->json([
            'data' => $new_detail,
        ], 200);
    }
}
