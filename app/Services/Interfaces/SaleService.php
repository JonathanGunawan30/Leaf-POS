<?php

namespace App\Services\Interfaces;

interface SaleService
{
    public function store($data);
    public function show($id);
    public function update($data, $id);

}
