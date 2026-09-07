<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ItemGroup extends Model
{
    use HasFactory;

    protected $table = 'item_group';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'gname',
        'isdeleted',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('isdeleted', 0);
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'group1', 'id');
    }
}
