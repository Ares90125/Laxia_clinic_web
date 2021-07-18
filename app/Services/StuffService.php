<?php
namespace App\Services;

use Illuminate\Support\Arr;
use App\Models\Stuff;
use DB;
use Auth;
use Throwable;

/**
 * Class StuffService
 * @package App\Services
 */
class StuffService
{
  public function paginate($search)
  {
    $per_page = isset($search['per_page']) ? $search['per_page'] : 20;
    $query = Stuff::query()
      ->with(['job', 'specialities']);
    if (isset($search['clinic_id'])) {
      $query->where('clinic_id', $search['clinic_id']);
    }
    $query->orderby('created_at', 'desc');
    return $query->paginate($per_page);
  }

  public function getList($search)
  {
    $query = Stuff::query()
      ->with(['job', 'specialities']);
    if (isset($search['clinic_id'])) {
      $query->where('clinic_id', $search['clinic_id']);
    }
    if (isset($search['q'])) {
      $query->where('name', 'LIKE', "%{$search['q']}%")
            ->orWhere('kana', 'LIKE', "%{$search['q']}%");
    }
    $query->orderby('created_at', 'desc');
    return $query->get();
  }

  public function getMemberCount($clinic_id, $request = array()){
    $query = Stuff::query();

    if (isset($request['clinic_id'])) {
      $query->where('clinic_id', $request['clinic_id']);
    }

    if (isset($request['q'])) {
      $query->where('name', 'LIKE', "%{$request['q']}%")
            ->orWhere('kana', 'LIKE', "%{$request['q']}%");
    }
    
    return $query->get()->count();
  }

  public function toArray($params)
  {
    $query = Stuff::query();
    if (isset($params['clinic_id'])) {
      $query->where('clinic_id', $params['clinic_id']);
    }
    $query->orderby('created_at', 'desc');
    return $query->get()
      ->pluck('name', 'id')
      ->toArray();
  }

  public function get($id)
  {
    return Stuff::findOrFail($id);
  }

  public function store($attributes, $addtional = [])
  {
    $stuffAttrs = Arr::get($attributes, 'stuffs');
    $specialitiesAttrs = Arr::get($attributes, 'specialities');
    
    $data = array_merge($stuffAttrs, $addtional);
    $stuff = Stuff::create($data);
    if ($specialitiesAttrs) {
      $stuff->specialities()->sync(array_filter($specialitiesAttrs));
    }

    return $stuff;
  }

  public function update($attributes, $where)
  {
    $stuffAttrs = Arr::get($attributes, 'stuffs');
    $stuff = Stuff::where($where)->firstOrFail();
    $stuff->fill($stuffAttrs);
    $stuff->save();

    $specialitiesAttrs = Arr::get($attributes, 'specialities');
    $stuff->specialities()->sync(array_filter($specialitiesAttrs));

    return $stuff;
  }
}
