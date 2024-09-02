<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id', 'name', 'active'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function product()
    {
        return $this->hasOne(Products::class, 'category_id', 'id');
    }
}
