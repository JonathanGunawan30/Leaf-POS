<?php

namespace App\Services\Implementations;

use App\Http\Requests\UpdateTaxRequest;
use App\Models\Tax;
use App\Services\Interfaces\TaxService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class TaxServiceImpl implements TaxService
{
    public function getTaxById($id): Tax
    {
        $data = Tax::find($id);

        if(!$data){
            throw new ModelNotFoundException();
        }

        return $data;
    }

    public function updateTax($id, UpdateTaxRequest $request): Tax
    {
        $tax = Tax::find($id);

        if (!$tax) {
            throw new ModelNotFoundException();
        }

        $data = $request->only(['name', 'rate']);

        $tax->update($data);

        return $tax;
    }

    public function getAll(): LengthAwarePaginator
    {
        $perPage = request()->get('per_page', 10);
        $search = request()->get('search');

        return Tax::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('rate', 'like', "%{$search}%");
        })->paginate($perPage);
    }

    public function softdelete($id)
    {
        $tax = Tax::find($id);

        if (!$tax) {
            throw new ModelNotFoundException();
        }

        if($tax->products()->withTrashed()->exists()){
            throw new \Exception("Tax cannot be deleted because it is associated with products");
        }

        if($tax->purchases()->withTrashed()->exists()){
            throw new \Exception("Tax cannot be deleted because it is associated with purchases");
        }

        $tax->delete();
        return $tax;

    }

    public function restore($id)
    {
        $tax = Tax::withTrashed()->find($id);

        if (!$tax) {
            throw new ModelNotFoundException();
        }

        if (!$tax->trashed()) {
            throw new \Exception('Cannot restore, tax has not been soft deleted.');
        }

        $tax->restore();
        return $tax;
    }

    public function hardDelete($id)
    {
        $tax = Tax::withTrashed()->find($id);

        if (!$tax) {
            throw new ModelNotFoundException();
        }

        if (!$tax->trashed()) {
            throw new \Exception('Cannot hard delete, tax has not been soft deleted.');
        }

        if ($tax->products()->withTrashed()->exists()) {
            throw new \Exception('Cannot hard delete, tax is still related to one or more products.');
        }

        if ($tax->purchases()->withTrashed()->exists()) {
            throw new \Exception('Cannot hard delete, tax is still related to one or more purchases.');
        }

        $tax->forceDelete();
        return $tax;
    }

    public function trashed()
    {
        $perPage = request()->get('per_page', 10);

        $search = request()->get('search');

        return Tax::onlyTrashed()->when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('rate', 'like', "%{$search}%");
        })->latest('deleted_at')
            ->paginate($perPage);
    }
}
