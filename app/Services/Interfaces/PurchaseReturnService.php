<?php

namespace App\Services\Interfaces;

interface PurchaseReturnService
{
    function create($data);
    function getAll();
    function find($id);
    function update($data, $id);
    function softDelete($id);
    function restore($id);
    function hardDelete($id);
    function trashed();
}
