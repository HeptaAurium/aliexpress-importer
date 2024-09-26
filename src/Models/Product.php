<?php

namespace Heptaaurium\AliexpressImporter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $table = 'ec_products';
    protected $fillable = [
        'description',
        'images',
        'image',
        'aliexpress_product_id',
        'name',
        'price',
        'quantity'
    ];
}
