<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommentDeparture;
use App\Models\Departure;
use App\Traits\DeleteModelTrait;
use Illuminate\Http\Request;

class CommentDepartureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use DeleteModelTrait;
    private $comment_departure;
    private $departure;
    public function __construct(CommentDeparture $comment_departure, Departure $departure)
    {
        $this->departure = $departure;
        $this->comment_departure = $comment_departure;
    }
    public function index(Request $request)
    {
        $pagesize = 10;
        $searchData = $request->except('page');
        if (count($request->all()) == 0) {
            // Lấy ra danh sách sản phẩm & phân trang cho nó
            $comment_departures = $this->comment_departure->orderBy('id', 'desc')->paginate($pagesize);
            $comment_departures->load('customer');
            $comment_departures->load('departure');
        } else {
            $comment_departures =  $this->comment_departure::whereHas('customer', function ($query) use ($request) {
                $query->where('first_name', 'LIKE', "%" . $request->first_name . "%");
            })->paginate($pagesize)->appends($searchData);
            $comment_departures = $comment_departures;
            $comment_departures->load('customer');
            $comment_departures->load('departure');
        }
        return response()->json($comment_departures, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comment_departures = $this->comment_departure->find($id);
        $comment_departures->load('customer');
        $comment_departures->load('departure');
        return response()->json([
            'data' => $comment_departures,
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
        $comment_departures = $this->comment_departure->find($id);
        $comment_departures->load('customer');
        $comment_departures->load('departure');
        $comment_departures->status = $request->status;
        $comment_departures->save();
        return response()->json([
            'data' => $comment_departures,
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
        $viewDelete = $this->comment_departure->onlyTrashed()->paginate(5);
        return response()->json($viewDelete, 200);
    }
    public function destroy($id)
    {
        return $this->deleteModelTrait($id, $this->comment_departure);
    }
    public function deleteChecked($id)
    {
        return $this->deleteCheckedModelTrait($id, $this->comment_departure);
    }
    public function restore($id)
    {
        return $this->restoreModelTrait($id, $this->comment_departure);
    }
    public function restoreAll()
    {
        return $this->restoreAllModelTrait($this->comment_departure);
    }
    public function forceDelete($id)
    {
        return $this->forceDeleteModelTrait($id, $this->comment_departure);
    }
}
