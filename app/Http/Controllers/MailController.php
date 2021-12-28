<?php

namespace App\Http\Controllers;

use App\Mail\InfoDeparture;
use App\Models\Departure;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendmail(Request $request)
    {
        $id = $request->id;
        $checkId = Invoice::where('id', $id)->first();
        $email = Invoice::where('id', $id)->value('email');
        $departureId = Invoice::where('id', $id)->value('departure_id');
        $departure = Departure::where('id', $departureId)->firts();
        if ($checkId) {
            Mail::to($email)->send(new InfoDeparture($email, $checkId, $departure));
            return new JsonResponse(
                [
                    'success' => true,
                    'message' => "Vé của bạn đã được gửi tới email"
                ],
                202
            );
        } else {
            return new JsonResponse(
                [
                    'success' => false,
                    'message' => "Vé của bạn gửi thất bại"
                ],
                404
            );
        }
    }
}