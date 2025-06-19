<?php

namespace App\Services\Implementations;

use App\Models\Customer;
use App\Models\Supplier;
use App\Services\Interfaces\CustomerService;
use Illuminate\Support\Facades\Request;
use mysql_xdevapi\Exception;

class CustomerServiceImpl implements CustomerService
{
    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function show($id): Customer
    {
        return Customer::withTrashed()->findOrFail($id);
    }

    public function update(array $data, $id): Customer
    {
        $customer = Customer::query()->findOrFail($id);
        $customer->update($data);
        return $customer;
    }

    public function getAll()
    {
        $perPage = Request()->get("per_page", 10);

        $query = Customer::query();

        $search = request()->get('search');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('company_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('city', 'like', '%' . $search . '%')
                    ->orWhere('country', 'like', '%' . $search . '%');
            });
        }

        return $query->paginate($perPage);
    }

    public function softdelete($id)
    {
        $customer = Customer::findOrFail($id);

        $customer->delete();

        return $customer;
    }

    public function restore($id)
    {
        $customer = Customer::withTrashed()->findOrFail($id);

        if(!$customer->trashed()){
            throw new \Exception("Cannot restore because customer is not deleted", 400);
        }

        $customer->restore();
        return $customer;

    }

    public function harddelete($id)
    {
        $customer = Customer::withTrashed()->findOrFail($id);

        if(!$customer->trashed()){
            throw new \Exception("Cannot hard delete because customer is not deleted", 400);
        }

        $customer->forceDelete();
        return $customer;
    }

    public function trashed()
    {
        $perPage = request()->get('per_page', 10);
        $query = Customer::onlyTrashed();
        $search = request()->get('search');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('company_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('city', 'like', '%' . $search . '%')
                    ->orWhere('country', 'like', '%' . $search . '%');
            });
        }
        return $query->latest('deleted_at')->paginate($perPage);
    }

}
