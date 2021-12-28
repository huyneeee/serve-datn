<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarImage;
use App\Models\Policy;
use App\Traits\DeleteModelTrait;
use App\Traits\StorageImageTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use StorageImageTrait;
    use DeleteModelTrait;
    private $car;
    public function __construct(Car $car)
    {
        $this->car = $car;
    }
    public function index(Request $request)
    {
        $pagesize = 10;
        $searchData = $request->except('page');
        if (count($request->all()) == 0) {
            // Lấy ra danh sách sản phẩm & phân trang cho nó
            $cars = $this->car->orderBy('id', 'desc')->paginate($pagesize);
            foreach ($cars as $item) {
                $cars_id = $item->policies;
            }
        } else {
            $carsQuery = $this->car->where('name', 'like', "%" . $request->keyword . "%");
            $cars = $carsQuery->paginate($pagesize)->appends($searchData);
            foreach ($cars as $item) {
                $cars_id = $item->policies;
            }
        }
        return response()->json($cars, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100|min:6',
            'description' => 'required',
            'license_plates' => 'required',
            'image' => 'mimes:jpg,bmp,png,jpeg|required',
            'number_seats' => 'required',
            'color' => 'required',
            'car_phone' => 'required|numeric|digits_between:10,11',
            'image_path' => 'required',
            'image_path.*' => 'mimes:jpg,png,jpeg,gif,svg',
            'policy_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }
        try {
            DB::beginTransaction();
            $dataCartCreate = [
                'name' => $request->name,
                'description' => $request->description,
                'license_plates' => $request->license_plates,
                'number_seats' => $request->number_seats,
                'status' => $request->status,
                'color' => $request->color,
                'car_phone' => $request->car_phone,

            ];
            $dataUpload = $this->storageTraitUpload($request, 'image', 'car');
            if (!empty($dataUpload)) {
                $dataCartCreate['image'] = $dataUpload['file_path'];
            }
            $car = $this->car->create($dataCartCreate);
            //insert data car_image
            if ($request->hasFile('image_path')) {
                foreach ($request->image_path as $fileItem) {
                    $dataCartImageDetail = $this->storageTraitUploadMutiple($fileItem, 'car');
                    $image =   CarImage::create([
                        'car_id' => $car->id,
                        'image_path' => $dataCartImageDetail['file_path'],
                    ]);
                }
            }

            // if (!empty($request->policy_id)) {
            //     foreach ($request->policy_id as $prolicy_Item) {
            //         $policyInstance = Policy::firstOrCreate(['name' => $prolicy_Item, 'detail' => $prolicy_Item]);
            //         $prolicyIds[] = $policyInstance->id;
            //     }
            // }
            //insert data Policy
            $car->policies()->attach($request->policy_id);
            // $car->policies()->attach($prolicyIds);
            DB::commit();
            return response()->json([
                'code' => 201,
                'data' => $car, $image
            ], status: 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("message" . $exception->getMessage() . 'Line:' . $exception->getLine());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $car_id =  $this->car->find($id);
        foreach ($car_id->policies as $item) {
        }
        return response()->json([
            'code' => 200,
            'data' => $car_id,
        ]);
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
            'name' => 'required|max:100|min:6',
            'description' => 'required',
            'license_plates' => 'required',
            'image' => 'mimes:jpg,bmp,png,jpeg|required',
            'number_seats' => 'required',
            'color' => 'required',
            'car_phone' => 'required|numeric|digits_between:10,11',
            'image_path' => 'required',
            'image_path.*' => 'mimes:jpg,png,jpeg,gif,svg',
            'policy_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }
        try {
            DB::beginTransaction();
            $dataCartCreate = [
                'name' => $request->name,
                'description' => $request->description,
                'license_plates' => $request->license_plates,
                'number_seats' => $request->number_seats,
                'status' => $request->status,
                'color' => $request->color,
                'car_phone' => $request->car_phone,
            ];
            $dataUpload = $this->storageTraitUpload($request, 'image', 'car');
            if (!empty($dataUpload)) {
                $dataCartCreate['image'] = $dataUpload['file_path'];
            }
            $this->car->find($id)->update($dataCartCreate);
            $car = $this->car->find($id);
            //insert data car_image
            if ($request->hasFile('image_path')) {
                CarImage::where('car_id', $id)->delete();
                foreach ($request->image_path as $fileItem) {
                    $dataCartImageDetail = $this->storageTraitUploadMutiple($fileItem, 'car');
                    $image =   CarImage::create([
                        'car_id' => $car->id,
                        'image_path' => $dataCartImageDetail['file_path'],
                    ]);
                }
            }

            // if (!empty($request->policy_id)) {
            //     foreach ($request->policy_id as $prolicy_Item) {
            //         $policyInstance = Policy::firstOrCreate(['name' => $prolicy_Item, 'detail' => $prolicy_Item]);
            //         $prolicyIds[] = $policyInstance->id;
            //     }
            // }
            //insert data Policy
            $car->policies()->sync($request->policy_id);
            // $car->policies()->attach($prolicyIds);
            DB::commit();
            return response()->json([
                'code' => 201,
                'data' => $car, $image
            ], status: 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("message" . $exception->getMessage() . 'Line:' . $exception->getLine());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function viewDelete(Request $request)
    {
        $cars = $this->car->onlyTrashed()->paginate(5);
        foreach ($cars as $item) {
            $cars_id = $item->policies;
        }
        return response()->json($cars, 200);
    }
    public function destroy($id)
    {
        //  $car_id = $this->car::find($id);
        // $car_id->car_images()->delete();
        // $car_id->policies()->detach();
        return $this->deleteModelTrait($id, $this->car);
    }
    public function forceDelete($id)
    {
        $car_id = $this->car::withTrashed()->find($id);
        $car_id->car_images()->delete();
        $car_id->policies()->detach();
        return $this->forceDeleteModelTrait($id, $this->car);
    }
    public function deleteChecked($id)
    {
        return $this->deleteCheckedModelTrait($id, $this->car);
    }
    public function restore($id)
    {
        return $this->restoreModelTrait($id, $this->car);
    }
    public function restoreAll()
    {
        return $this->restoreAllModelTrait($this->car);
    }
}
