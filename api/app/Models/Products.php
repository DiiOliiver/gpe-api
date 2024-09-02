<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'name', 'description', 'price',
        'expiration_date', 'image', 'active', 'category_id'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function category()
    {
        return $this->hasOne(Categories::class, 'id', 'category_id');
    }
}
