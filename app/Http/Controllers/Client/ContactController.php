<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    private $contact;
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }
    public function contactAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric|digits_between:10,11',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }
        $contactAll = $this->contact;
        $contactAll->fill($request->all());
        $contactAll->save();
        return response()->json([
            'code' => 201,
            'data' => $contactAll,
        ]);
    }
}
