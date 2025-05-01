<?php

namespace App\Services\Interfaces;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryService
{
    public function getCategoryById($id);
    public function updateCategory($id, array $data): Category;
    public function getAll(): LengthAwarePaginator;
    public function softdelete($id);
    public function restore($id);
    public function force($id);
    public function trashed();
}
