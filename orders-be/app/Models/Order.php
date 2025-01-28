<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    const CREATED_AT = 'date';
    const UPDATED_AT = null;

    protected $table = "order";
    protected $fillable = ['name', 'description'];

    public function products(){
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }
}
