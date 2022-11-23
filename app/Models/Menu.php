<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
    ];

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'transaction_menus')
            ->withPivot(['quantity', 'total', 'price']);
    }
}
