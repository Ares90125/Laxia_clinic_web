<?php
namespace App\Services;

use Illuminate\Support\Arr;
use App\Models\Master\Category;
use App\Models\Menu;
use DB;
use Auth;
use Throwable;

/**
 * Class MenuService
 * @package App\Services
 */
class MenuService
{
  public function paginate($search)
  {
    $per_page = isset($search['per_page']) ? $search['per_page'] : 20;
    $query = Menu::query()
      ->with([
        'categories',
        'clinic',
        'images'
      ]);

    if (isset($search['clinic_id'])) {
      $query->where('clinic_id', $search['clinic_id']);
    }

    if (isset($search['status'])) {
      $query->where('status', $search['status']);
    } else {
      $query->where('status', '1');
    }

    // if (isset($search['category_id'])) {
    //   if (is_array($search['category_id'])) {
    //     if (!empty(array_filter($search['category_id']))) {
    //       $query->whereIn('category_id', $search['category_id']);
    //     }
    //   } else if ($search['category_id'] != '-1') {
    //     $category = Category::find($search['category_id']);
    //     $ids = $category->descendantsAndSelf()->pluck('id');
    //     $query->whereIn('category_id', $ids);
    //   }
    // }

    if (isset($search['category_id']) && $search['category_id'] != '-1') {
      // $query->where('category_id', $search['category_id']);
      $query->join('menu_categories as mc', 'menus.id', '=', 'mc.menu_id')
            ->where('mc.category_id', $search['category_id']);
    }

    $query->orderby('created_at', 'desc');

    return $query->paginate($per_page);
  }

  public function toArray($params)
  {
    $query = Menu::query()
      ->public();
    if (isset($params['clinic_id'])) {
      $query->where('clinic_id', $params['clinic_id']);
    }
    $query->orderby('created_at', 'desc');
    return $query->get()
      ->pluck('name', 'id')
      ->toArray();
  }

  public function toArrayDetail($params)
  {
    $query = Menu::query()
      ->public();
    if (isset($params['clinic_id'])) {
      $query->where('clinic_id', $params['clinic_id']);
    }
    $query->orderby('created_at', 'desc');
    return $query->get(['id','name','price']);
  }

  public function get($id)
  {
    return Menu::with([
      'images',
      'category'
    ])
    ->findOrFail($id);
  }

  public function store($attributes, $addtional = [])
  {
    $menuAttrs = Arr::get($attributes, 'menus');
    $data = array_merge($menuAttrs, $addtional);
    $menu =  Menu::create($data);

    $menuPhotos = Arr::get($attributes, 'menuPhotos');
    $menu->images()->delete();
    foreach ($menuPhotos as $photo) {
      $menu->images()->create([
        'path' => $photo
      ]);
    }

    $categoryAttrs = Arr::get($attributes, 'categories');
    $menu->categories()->sync($categoryAttrs);

    return $menu;
  }

  public function update($attributes, $where)
  {
    $menuAttrs = Arr::get($attributes, 'menus');
    $menu = Menu::where($where)->firstOrFail();
    $menu->update($menuAttrs);

    $menuPhotos = Arr::get($attributes, 'menuPhotos');
    $menu->images()->delete();
    foreach ($menuPhotos as $photo) {
      $menu->images()->create([
        'path' => $photo
      ]);
    }

    $categoryAttrs = Arr::get($attributes, 'categories');
    $menu->categories()->sync($categoryAttrs);
    // $menu->images()->sync($menuPhotos);
    return $menu;
  }
}
