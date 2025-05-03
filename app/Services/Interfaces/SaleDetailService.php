<?php

namespace App\Services\Interfaces;

interface SaleDetailService
{
    public function store($data, $saleId);
    public function delete($saleId, $saleDetailId);

}
