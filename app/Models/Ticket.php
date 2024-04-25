<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $table = 'tbl_tickets';

    public $fillable = [
        'ticket',
        'food_name'
    ];

    public $timestamps = false;
}
