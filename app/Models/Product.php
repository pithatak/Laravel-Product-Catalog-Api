<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category',
        'name',
        'manufacturer',
        'price',
        'description',
    ];

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }
}
