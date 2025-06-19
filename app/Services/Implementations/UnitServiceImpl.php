<?php

namespace App\Services\Implementations;

use App\Models\Unit;
use App\Services\Interfaces\UnitService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;


class UnitServiceImpl implements UnitService
{

    public function getDetail($id)
    {
        $unit = Unit::find($id);

        if(!$unit){
            throw new ModelNotFoundException();
        }

        return $unit;
    }

    public function update($data, $id): Unit
    {
        $unit = Unit::find($id);

        if(!$unit){
            throw new ModelNotFoundException();
        }

        $unit->update($data);
        return $unit;
    }

    public function getAll(): LengthAwarePaginator
    {
        $perPage = request()->get('per_page', 10);
        $search = request()->get('search');
        return Unit::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
            ->orWhere('code', 'like', "%{$search}%");
        })->paginate($perPage);
    }

    public function deleteById($id)
    {
        $unit = Unit::find($id);

        if(!$unit){
            throw new ModelNotFoundException();
        }

        if ($unit->products()->withTrashed()->exists()) {
            throw new \Exception("Cannot delete unit because it has related products");
        }

        $unit->delete();
        return $unit;
    }

    public function restore($id)
    {
        $unit = Unit::withTrashed()->find($id);

        if (!$unit) {
            throw new ModelNotFoundException("Unit not found.");
        }

        if (!$unit->trashed()) {
            throw new \Exception("Unit is not deleted");
        }

        $unit->restore();

        return $unit;
    }

    public function force($id)
    {
        $unit = Unit::withTrashed()->find($id);

        if (!$unit) {
            throw new ModelNotFoundException("Unit not found.");
        }

        if ($unit->products()->withTrashed()->exists()) {
            throw new \Exception("Cannot force delete unit because it has related products");
        }

        if (!$unit->trashed()) {
            throw new \Exception("Unit is not deleted");
        }

        $unit->forceDelete();
        return $unit;
    }

    public function trashed()
    {
        $perPage = request()->get('per_page', 10);
        $search = request()->get('search');
        return Unit::onlyTrashed()->when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
            ->orWhere('code', 'like', "%{$search}%");
        })->latest('deleted_at')
            ->paginate($perPage);
    }
}
