<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait DeleteModelTrait
{
    public function forceDeleteModelTrait($id, $model)
    {
        try {
            $model->withTrashed()->find($id)->forceDelete();
            return response()->json([
                'code' => 200,
                'message' => 'success'
            ]);
        } catch (\Exception $exception) {
            Log::error("message:" . $exception->getMessage() . '--Line:' . $exception->getLine());
            return response()->json([
                'code' => 500,
                'message' => 'fail'
            ]);
        }
    }
    public function deleteModelTrait($id, $model)
    {
        try {
            $model->find($id)->delete();
            return response()->json([
                'code' => 200,
                'message' => 'success'
            ]);
        } catch (\Exception $exception) {
            Log::error("message:" . $exception->getMessage() . '--Line:' . $exception->getLine());
            return response()->json([
                'code' => 500,
                'message' => 'fail'
            ]);
        }
    }
    public function deleteCheckedModelTrait($id, $model)
    {
        try {
            $ids = explode(",", $id);
            $model->whereIn('id', $ids)->delete();
            return response()->json([
                'code' => 200,
                'message' => 'success'
            ]);
        } catch (\Exception $exception) {
            Log::error("message:" . $exception->getMessage() . '--Line:' . $exception->getLine());
            return response()->json([
                'code' => 500,
                'message' => 'fail'
            ]);
        }
    }
    public function restoreModelTrait($id, $model)
    {
        try {
            $model->withTrashed()->find($id)->restore();
            return response()->json([
                'code' => 200,
                'message' => 'success'
            ]);
        } catch (\Exception $exception) {
            Log::error("message:" . $exception->getMessage() . '--Line:' . $exception->getLine());
            return response()->json([
                'code' => 500,
                'message' => 'fail'
            ]);
        }
    }
    public function restoreAllModelTrait($model)
    {
        try {
            $model->onlyTrashed()->restore();
            return response()->json([
                'code' => 200,
                'message' => 'success'
            ]);
        } catch (\Exception $exception) {
            Log::error("message:" . $exception->getMessage() . '--Line:' . $exception->getLine());
            return response()->json([
                'code' => 500,
                'message' => 'fail'
            ]);
        }
    }
}