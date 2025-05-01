<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes, HasFactory;
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;
    protected $table = "suppliers";

    protected $fillable = [
        "name", "company_name", "email", "phone", "city", "postal_code", "province", "country", "address",
        "bank_account", "bank_name", "npwp_number", "siup_number", "nib_number", "business_type", "note"
    ];


    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, "supplier_id", "id");
    }

}
