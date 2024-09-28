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
        'quantity',
        'imported_from_aliexpress',
        'created_by_id',
        'with_storehouse_management',
        'is_featured',
        'is_variation',
        'stock_status'
    ];
}
