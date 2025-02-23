<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
    public $timestamps = false;
    
    protected $fillable = ['name', 'price'];

    public function orders(){
        return $this->belongsToMany(Order::class);
    }

    public function stock(){
        return $this->hasOne(Stock::class);
    }
}
