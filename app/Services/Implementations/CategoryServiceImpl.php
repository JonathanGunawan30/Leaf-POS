<?php

namespace App\Services\Implementations;

use App\Models\Category;
use App\Services\Interfaces\CategoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryServiceImpl implements CategoryService
{
    public function getCategoryById($id)
    {
        $category = Category::find($id);

        if(!$category){
            throw new ModelNotFoundException();
        }

        return $category;
    }
    public function updateCategory($id, array $data): Category
    {
        $category = Category::find($id);

        if (!$category) {
            throw new ModelNotFoundException();
        }

        $category->fill($data);
        $category->save();

        return $category;
    }

    public function getAll(): LengthAwarePaginator
    {
        $perPage = request()->get('per_page', 10);
        return Category::paginate($perPage);
    }

    public function softdelete($id)
    {
        $category = Category::find($id);

        if (!$category) {
            throw new ModelNotFoundException();
        }

        if ($category->products()->withTrashed()->exists()) {
            throw new \Exception("Cannot delete category because it is still associated with one or more products.");
        }

        $category->delete();
        return $category;
    }

    public function restore($id)
    {
        $category = Category::withTrashed()->find($id);

        if (!$category) {
            throw new ModelNotFoundException();
        }

        if(!$category->trashed()){
            throw new \Exception("Cannot restore. Category has not been soft deleted.");
        }

        $category->restore();
        return $category;
    }

    public function force($id)
    {
        $category = Category::withTrashed()->find($id);
        if (!$category) {
            throw new ModelNotFoundException();
        }

        if(!$category->trashed()){
            throw new \Exception("Cannot force delete. Category has not been soft deleted.");
        }

        if($category->products()->withTrashed()->exists()){
            throw new \Exception("Cannot force delete category because it is still associated with one or more products.");
        }

        $category->forceDelete();
        return $category;
    }

    public function trashed()
    {
        $perPage = request()->get('per_page', 10);
        return Category::onlyTrashed()->latest("deleted_at")->paginate($perPage);
    }
}
