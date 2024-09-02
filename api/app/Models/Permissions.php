<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permissions extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id', 'name', 'description', 'active'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Roles::class);
    }
}
