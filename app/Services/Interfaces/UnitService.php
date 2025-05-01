<?php

namespace App\Services\Interfaces;

use App\Models\Unit;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

interface UnitService
{
    public function getDetail($id);
    public function update($data, $id): Unit;
    public function getAll(): LengthAwarePaginator;
    public function deleteById($id);
    public function restore($id);
    public function force($id);
    public function trashed();
}
