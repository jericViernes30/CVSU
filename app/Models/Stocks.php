<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stocks extends Model
{
    use HasFactory;

    protected $fillable = [
        'item',
        'category',
        'supplier',
        'product_unit',
        'barcode',
        'quantity',
        'cost',
        'retail',
        'update_reason'
    ];

    public function ticket()
    {
        return $this->hasOne(Ticket::class, 'food_name', 'item');
    }
}
