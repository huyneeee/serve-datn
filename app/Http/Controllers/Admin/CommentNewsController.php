<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommentNews;
use App\Models\News;
use App\Traits\DeleteModelTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentNewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use DeleteModelTrait;
    private $comment_new;
    private $news;
    public function __construct(CommentNews $comment_new, News $news)
    {
        $this->comment_new = $comment_new;
        $this->news = $news;
    }
    public function index(Request $request)
    {
        $pagesize = 10;
        $searchData = $request->except('page');
        if (count($request->all()) == 0) {
            // Lấy ra danh sách sản phẩm & phân trang cho nó
            $comment_news = $this->comment_new->orderBy('id', 'desc')->paginate($pagesize);
            $comment_news->load('customer');
            $comment_news->load('new');
        } else {
            $comment_news =  $this->comment_new::whereHas('customer', function ($query) use ($request) {
                $query->where('first_name', 'LIKE', "%" . $request->first_name . "%");
            })->paginate($pagesize)->appends($searchData);
            $comment_news = $comment_news;
            $comment_news->load('customer');
            $comment_news->load('new');
        }
        return response()->json($comment_news, 200);
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
        $comment_news = $this->comment_new->find($id);
        $comment_news->load('customer');
        $comment_news->load('new');
        return response()->json([
            'data' => $comment_news,
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
        $comment_news = $this->comment_new->find($id);
        $comment_news->load('customer');
        $comment_news->load('new');
        $comment_news->status = $request->status;
        $comment_news->save();
        return response()->json([
            'data' => $comment_news,
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
        $viewDelete = $this->comment_new->onlyTrashed()->paginate(5);
        return response()->json($viewDelete, 200);
    }
    public function destroy($id)
    {
        return $this->deleteModelTrait($id, $this->comment_new);
    }
    public function deleteChecked($id)
    {
        return $this->deleteCheckedModelTrait($id, $this->comment_new);
    }
    public function restore($id)
    {
        return $this->restoreModelTrait($id, $this->comment_new);
    }
    public function restoreAll()
    {
        return $this->restoreAllModelTrait($this->comment_new);
    }
    public function forceDelete($id)
    {
        return $this->forceDeleteModelTrait($id, $this->comment_new);
    }
}
