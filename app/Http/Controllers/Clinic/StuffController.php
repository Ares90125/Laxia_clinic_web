<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StuffService;
use App\Http\Requests\StuffRequest;
use App\Models\Stuff;
use App\Traits\MediaUpload;
use Illuminate\Support\Str;

class StuffController extends Controller
{
    use MediaUpload;

    /**
     * @var StuffService
     */
    protected $service;

    public function __construct(
        StuffService $service
    ) {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $params['clinic_id'] = auth()->guard('clinic')->user()->clinic->id;
        $stuffs = $this->service->paginate($params);
        return response()->json([
            'stuffs' => $stuffs
        ], 200);
    }

    public function getAll(Request $request)
    {
        $params = $request->all();
        $params['clinic_id'] = auth()->guard('clinic')->user()->clinic->id;
        $stuffs = $this->service->getList($params);
        $cnt_g = $this->service->getMemberCount($params['clinic_id']);
        return response()->json([
            'stuffs' => $stuffs,
            'cnt_mg' => $cnt_g
        ], 200);
    }

    public function store(StuffRequest $request)
    {
        $currentUser = auth()->guard('clinic')->user()->clinic;

        \DB::beginTransaction();
        try {
            $stuff = $this->service->store($request->all(), ['clinic_id' => $currentUser->id]);

            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error($e->getMessage());

            return response()->json([
                'message' => 'スタッフを登録できません。'
            ], 500);
        }
        return response()->json([
            'stuff' => $stuff
        ], 200);
    }

    public function update(StuffRequest $request, $id)
    {
        $params = $request->stuffs;
        $stuff = $this->service->get($id);
        $currentUser = auth()->guard('clinic')->user()->clinic;
        if ($currentUser->id != $stuff->clinic_id) {
            return response()->json([
                'message' => '権限がありません。'
            ], 403);
        }

        \DB::beginTransaction();
        try {
            $stuff = $this->service->update($request->all(), ['id' => $id]);

            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error($e->getMessage());

            return response()->json([
                'message' => 'スタッフを変更できません。'
            ], 500);
        }
        return response()->json([
            'stuff' => $stuff
        ], 200);
    }

    public function delete($id) {
        $stuff = Stuff::where('id', $id)->firstOrFail();
        $stuff->specialities()->delete();
        $stuffInfo = Stuff::where('id', $id);
        $stuffInfo->delete();
        
        return true;
    }

    public function uploadPhoto(Request $request)
    {
        $uploadedFile = $request->file;
        // $request->validate([
        //     'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        // ]);
        $disk = 'public';
        $filename = null;
        $name = !is_null($filename) ? $filename : Str::random(25);
        $file = $uploadedFile->storeAs('/clinic/stuffs', $name.'.'.$uploadedFile->getClientOriginalExtension(), $disk);

       return response()->json([
           'photo' => $file,
       ], 200);
    }
    
}
