<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $incrementing = false;
    protected $keyType = 'int';
    protected $fillable = [
        'id',
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
