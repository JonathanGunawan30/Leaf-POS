<?php

namespace App\Services\Interfaces;

interface PurchaseService
{
    public function store($data);
    public function show($id);
    public function update($data, $id);
    public function getAll();
    public function softdelete($id);
    public function restore($id);
    public function trashed();
    public function harddelete($id);
}
