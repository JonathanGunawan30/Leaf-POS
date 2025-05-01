<?php

namespace App\Services\Interfaces;

interface PurchasePaymentService
{
    public function create($data);
    public function show($id);

    public function update($data, $id);
    public function getAll();
    public function softdelete($id);
    public function restore($id);
    public function harddelete($id);

    public function trashed();

}
