<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewCategory;
use App\Traits\DeleteModelTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\StorageImageTrait;
use Illuminate\Support\Facades\Validator;

class NewCategoryController extends Controller
{
    use StorageImageTrait;
    use DeleteModelTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $newCategory;
    public function __construct(NewCategory $newCategory)
    {
        $this->newCategory = $newCategory;
    }
    public function index(Request $request)
    {
        $pagesize = 10;
        $searchData = $request->except('page');
        if (count($request->all()) == 0) {
            // Lấy ra danh sách sản phẩm & phân trang cho nó
            $newCategorys = $this->newCategory->orderBy('id', 'desc')->paginate($pagesize);
        } else {
            $newCategorysQuery = $this->newCategory->where('name', 'like', "%" . $request->keyword . "%");
            $newCategorys = $newCategorysQuery->orderBy('id', 'desc')->paginate($pagesize)->appends($searchData);
        }
        return response()->json($newCategorys, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function parent()
    {
        $optionSelect = $this->newCategory::where('parent_id', 0)->with(['children'])->get();
        // foreach ($optionSelect as $item) {
        //     $optionSelect_id = $item->children;
        // }
        return response()->json([
            'data' => $optionSelect,
        ]);
    }

    public function uniqueSlug(Request $request)
    {
        $slug = Str::slug($request->name);
        $count = $this->newCategory::where('slug', 'LIKE', "{$slug}%")->count();
        $newCount = $count > 0 ? ++$count : '';
        return $newCount > 0 ? "$slug-$newCount" : $slug;
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'parent_id' => 'required',
            //'image' => 'mimes:jpg,bmp,png,jpeg|required',
            'short_content' => 'required',
            'content' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        //  $dataImageNewCategory = $this->storageTraitUpload($request, 'image', 'new_categories');
        $dataInssert = $this->newCategory->create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'short_content' => $request->short_content,
            'content' => $request->content,
            'image' =>  $request->image,
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
        $newCategory_id =  $this->newCategory->find($id);
        return response()->json([
            'data' => $newCategory_id,
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
            'parent_id' => 'required',
            //  'image' => 'mimes:jpg,bmp,png,jpeg|required',
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
            'parent_id' => $request->parent_id,
            'short_content' => $request->short_content,
            'content' => $request->content,
            'image' => $request->image,
            'slug' => $this->uniqueSlug($request),
        ];
        // $dataImageNewCategory = $this->storageTraitUpload($request, 'image', 'new_categories');
        // if (!empty($dataImageNewCategory)) {
        //     $dataUpdate['image'] = $dataImageNewCategory['file_path'];
        // }
        $this->newCategory->find($id)->update($dataUpdate);
        return response()->json([
            'data' => $dataUpdate,
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
        $newCategories = $this->newCategory->onlyTrashed()->paginate(5);
        return response()->json($newCategories, 200);
    }

    public function destroy($id)
    {
        return $this->deleteModelTrait($id, $this->newCategory);
    }
    public function forceDelete($id)
    {
        return $this->forceDeleteModelTrait($id, $this->newCategory);
    }
    public function deleteChecked($id)
    {
        return $this->deleteCheckedModelTrait($id, $this->newCategory);
    }
    public function restore($id)
    {
        return $this->restoreModelTrait($id, $this->newCategory);
    }
    public function restoreAll()
    {
        return $this->restoreAllModelTrait($this->newCategory);
    }
}
