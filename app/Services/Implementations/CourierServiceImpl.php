<?php

namespace App\Services\Implementations;

use App\Models\Courier;
use App\Services\Interfaces\CourierService;
use Illuminate\Http\Request;

class CourierServiceImpl implements CourierService
{
    public function store($data): Courier
    {
        return Courier::create($data);
    }

    public function show($id)
    {
        return Courier::findOrFail($id);
    }

    public function update($data, $id)
    {
        $courier = $this->show($id);
        $courier->update($data);
        return $courier;
    }

    public function getAll()
    {
        $perPage = request()->get("per_page", 10);
        $search = request()->get("search");
        $status = request()->get("status");

        $query = Courier::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%');
        }

        if (!is_null($status)) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    public function softdelete($id)
    {
        $courier = $this->show($id);
        $courier->delete();
        return $courier;
    }

    public function restore($id)
    {
        $courier = Courier::withTrashed()->findOrFail($id);

        if(!$courier->trashed()){
            throw new \Exception("Cannot restore, courier is not deleted", 400);
        }
        $courier->restore();
        return $courier;
    }

    public function harddelete($id)
    {
        $courier = Courier::withTrashed()->findOrFail($id);

        if(!$courier->trashed()){
            throw new \Exception("Cannot hard delete, courier is not deleted", 400);
        }
        $courier->forceDelete();
    }

    public function trashed()
    {
        $perPage = request()->get('per_page', 10);
        $search = request()->get('search');
        $status = request()->get('status');

        $query = Courier::onlyTrashed()->latest('deleted_at');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%');
        }

        if (!is_null($status)) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

}
