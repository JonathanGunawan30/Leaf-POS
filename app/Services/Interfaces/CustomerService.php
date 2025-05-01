<?php

namespace App\Services\Interfaces;

use App\Models\Customer;

interface CustomerService
{
    public function create(array $data): Customer;
    public function show($id): Customer;
    public function update(array $data, $id): Customer;
    public function getAll();
    public function softdelete($id);
    public function restore($id);
    public function harddelete($id);
    public function trashed();

}
