<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CaseService;
use App\Http\Requests\CaseRequest;
use App\Traits\MediaUpload;
use Illuminate\Support\Str;

class CaseController extends Controller
{
    use MediaUpload;

    /**
     * @var CaseService
     */
    protected $service;

    public function __construct(
        CaseService $service
    ) {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $params['clinic_id'] = auth()->guard('clinic')->user()->clinic->id;
        $cases = $this->service->paginate($params);
        return response()->json([
            'cases' => $cases
        ], 200);
    }

    public function store(CaseRequest $request)
    {
        $currentUser = auth()->guard('clinic')->user()->clinic;
        \DB::beginTransaction();
        try {
            $case = $this->service->store($request->all(), ['clinic_id' => $currentUser->id]);

            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error($e->getMessage());

            return response()->json([
                'message' => '症例を登録できません。'
            ], 500);
        }
        return response()->json([
            'case' => $case
        ], 200);
    }

    public function update(CaseRequest $request, $id)
    {
        \DB::beginTransaction();
        try {
            $case = $this->service->update($request->all(), ['id' => $id]);

            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error($e->getMessage());

            return response()->json([
                'message' => '症例を変更できません。'
            ], 500);
        }
        return response()->json([
            'case' => $case
        ], 200);
    }

//     public function uploadBeforePhoto(Request $request)
//     {
//         // dd($request->file);
//         // var_dump("asdff");
// //        $path = $this->mediaUploadWithThumb('/clinic/cases', $request->file, 150);
// //        var_dump($path);
// //        return response()->json([
// //            'photo' => $path[1]
// //        ], 200);
    
//         $uploadedFile = $request->file;
//         // $request->validate([
//         //     'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//         // ]);

//         $disk = 'public';
//         $filename = null;
//         $name = !is_null($filename) ? $filename : Str::random(25);
//         $file = $uploadedFile->storeAs('/clinic/cases/before', $name.'.'.$uploadedFile->getClientOriginalExtension(), $disk);

//        return response()->json([
//            'status' => 1,
//            'photo' => $file,
//        ]);
//     }

    public function uploadBeforePhoto(Request $request)
    {
        $path = $this->mediaUploadWithThumb('/clinic/cases/before', $request->file, 250);        
        return response()->json([
            'status' => 1,
            'photo' => $path[1]
        ], 200);
    }

    // public function uploadAfterPhoto(Request $request)
    // {    
    //     $uploadedFile = $request->file;
    //     // $request->validate([
    //     //     'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //     // ]);
    //     $disk = 'public';
    //     $filename = null;
    //     $name = !is_null($filename) ? $filename : Str::random(25);
    //     $file = $uploadedFile->storeAs('/clinic/cases/after', $name.'.'.$uploadedFile->getClientOriginalExtension(), $disk);

    //    return response()->json([
    //        'status' => 1,
    //        'photo' => $file,
    //    ]);
    // }

    public function uploadAfterPhoto(Request $request)
    {
        $path = $this->mediaUploadWithThumb('/clinic/cases/after', $request->file, 250);        
        return response()->json([
            'status' => 1,
            'photo' => $path[1]
        ], 200);
    }
}
