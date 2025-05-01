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
        return Customer::findOrFail($id);
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

        $filters = Request()->only(["name", "company_name", "city", "country"]);

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
        return Customer::onlyTrashed()->latest("deleted_at")->paginate($perPage);
    }

}
