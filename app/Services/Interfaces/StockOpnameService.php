<?php

namespace App\Services\Interfaces;

interface StockOpnameService
{
    public function store($data);
    public function getAll($request);
    public function show($id);
    public function update($data, $id);
    public function delete($id);
    public function restore($id);
    public function forceDelete($id);
    public function trashed();
}
