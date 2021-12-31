<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewCategory;
use App\Models\News;
use App\Traits\DeleteModelTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\StorageImageTrait;
use Illuminate\Support\Facades\Validator;

class NewController extends Controller
{
    use StorageImageTrait;
    use DeleteModelTrait;
    private $newCategoryRecusive;
    private $news;
    private $newCategory;
    public function __construct(News $news, NewCategory $newCategory)
    {
        $this->news = $news;
        $this->newCategory = $newCategory;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagesize = 10;
        $searchData = $request->except('page');
        if (count($request->all()) == 0) {
            // Lấy ra danh sách sản phẩm & phân trang cho nó
            $news = $this->news->orderBy('id', 'desc')->paginate($pagesize);
        } else {
            $newsQuery = $this->news->where('name', 'like', "%" . $request->keyword . "%");
            if ($request->has('new_cate') && $request->new_cate != "") {
                $newsQuery = $newsQuery->where('new_cate', $request->new_cate);
            }
            $news = $newsQuery->paginate($pagesize)->appends($searchData);
        }
        return response()->json($news, 200);
    }

    public function parent()
    {
        $optionSelect = $this->newCategory::where('parent_id', 0)->with(['children'])->get();
        return response()->json([
            'data' => $optionSelect,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function uniqueSlug(Request $request)
    {
        $slug = Str::slug($request->name);
        $count = $this->news::where('slug', 'LIKE', "{$slug}%")->count();
        $newCount = $count > 0 ? ++$count : '';
        return $newCount > 0 ? "$slug-$newCount" : $slug;
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'new_cate' => 'required',
            'image' => 'mimes:jpg,bmp,png,jpeg|required',
            'act' => 'boolean',
            'hot' => 'boolean',
            'short_content' => 'required',
            'content' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        $dataImageNews = $this->storageTraitUpload($request, 'image', 'news');
        $dataInssert = $this->news->create([
            'name' => $request->name,
            'new_cate' => $request->new_cate,
            'short_content' => $request->short_content,
            'content' => $request->content,
            'act' => $request->act,
            'hot' => $request->hot,
            'image' =>  $dataImageNews['file_path'],
            'slug' => $this->uniqueSlug($request),
        ]);
        return response()->json([
            'data' => $dataInssert,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $news_id =  $this->news->find($id);
        return response()->json([
            'data' => $news_id,
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'new_cate' => 'required',
            'image' => 'mimes:jpg,bmp,png,jpeg|required',
            'act' => 'boolean',
            'hot' => 'boolean',
            'short_content' => 'required',
            'content' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        $dataUpdate = [
            'name' => $request->name,
            'new_cate' => $request->new_cate,
            'short_content' => $request->short_content,
            'content' => $request->content,
            'act' => $request->act,
            'hot' => $request->hot,
            'slug' => $this->uniqueSlug($request),
        ];
        $dataImageNews = $this->storageTraitUpload($request, 'image', 'news');
        if (!empty($dataImageNews)) {
            $dataUpdate['image'] = $dataImageNews['file_path'];
        }
        $this->news->find($id)->update($dataUpdate);
        return response()->json([
            'data' => $dataUpdate,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function viewDelete(Request $request)
    {
        $news_delete = $this->news->onlyTrashed()->paginate(5);
        return response()->json($news_delete, 200);
    }
    public function destroy($id)
    {
        return $this->deleteModelTrait($id, $this->news);
    }
    public function forceDelete($id)
    {
        return $this->forceDeleteModelTrait($id, $this->news);
    }
    public function deleteChecked($id)
    {
        return $this->deleteCheckedModelTrait($id, $this->news);
    }
    public function restore($id)
    {
        return $this->restoreModelTrait($id, $this->news);
    }
    public function restoreAll()
    {
        return $this->restoreAllModelTrait($this->news);
    }
}
