<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CommentDeparture;
use Illuminate\Http\Request;
use App\Models\CommentNews;
use App\Models\Departure;
use App\Models\News;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $comment_new;
    private $news;
    private $departure;
    private $comment_departure;
    public function __construct(CommentNews $comment_new, News $news, Departure $departure, CommentDeparture $comment_departure)
    {
        $this->comment_new = $comment_new;
        $this->news = $news;
        $this->departure = $departure;
        $this->comment_departure = $comment_departure;
    }
    public function viewCommentNew()
    {
        $comment_news = $this->comment_new::where('status', '<>', 0)->get();
        return response()->json([
            'data' => $comment_news,
        ], 200);
    }
    public function commentNew($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        $new_id = $id;
        $new = $this->news::find($id);
        $model = $this->comment_new;
        $model->news_id = $new_id;
        $model->content = $request->content;
        $model->customer_id = Auth::user()->id;
        $model->status = 0;
        $model->save();
        return response()->json([
            'data' => $model,
        ], 201);
    }

    public function viewCommentDeparture()
    {
        $comment_departures = $this->comment_departure::where('status', '<>', 0)->get();
        foreach ($comment_departures as $item) {
            $item->customer;
        }
        return response()->json([
            'data' => $comment_departures,
        ], 200);
    }

    public function commentDeparture($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'star' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        $departureId = $id;
        $departure = $this->departure::find($id);
        $model = $this->comment_departure;
        $model->departure_id = $departureId;
        $model->content = $request->content;
        $model->customer_id = Auth::user()->id;
        $model->star = $request->star;
        $model->status = 0;
        $model->save();
        return response()->json([
            'data' => $model,
        ], 201);
    }
}
