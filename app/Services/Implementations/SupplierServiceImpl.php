<?php

namespace App\Services\Implementations;

use App\Models\Supplier;
use App\Services\Interfaces\SupplierService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SupplierServiceImpl implements SupplierService
{
    public function getSupplierById($id): Supplier
    {
        $supplier = Supplier::find($id);
        if(!$supplier){
            throw new ModelNotFoundException();
        }
        return $supplier;
    }

    public function updateSupplier($id, $data)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update($data);
        return $supplier;
    }

    public function getAll(array $filters = [], $perPage = 10)
    {
        $query = Supplier::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['company_name'])) {
            $query->where('company_name', 'like', '%' . $filters['company_name'] . '%');
        }

        if (!empty($filters['city'])) {
            $query->where('city', 'like', '%' . $filters['city'] . '%');
        }

        if (!empty($filters['country'])) {
            $query->where('country', 'like', '%' . $filters['country'] . '%');
        }

        return $query->paginate($perPage);
    }

    public function deleteSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);

        if($supplier->purchases()->withTrashed()->exists()){
            throw new \Exception("Cannot delete supplier because it is still associated with one or more purchases.");
        }

        $supplier->delete();
        return $supplier;
    }

    public function restore($id)
    {
        $supplier = Supplier::withTrashed()->find($id);

        if (!$supplier) {
            throw new ModelNotFoundException();
        }

        if (!$supplier->trashed()) {
            throw new \Exception("Supplier is not deleted, cannot be restored.", 400);
        }

        $supplier->restore();

        return $supplier;

    }

    public function force($id)
    {
        $supplier = Supplier::withTrashed()->find($id);
        if (!$supplier) {
            throw new ModelNotFoundException();
        }

        if (!$supplier->trashed()) {
            throw new \Exception("Supplier is not deleted, cannot be hard deleted.", 400);
        }

        if($supplier->purchases()->withTrashed()->exists()){
            throw new \Exception("Cannot force delete supplier because it is still associated with one or more purchases.");
        }

        $supplier->forceDelete();
        return $supplier;
    }

    public function trashed()
    {
        $perPage = request()->get('per_page', 10);
        return Supplier::onlyTrashed()->latest("deleted_at")->paginate($perPage);
    }

}

