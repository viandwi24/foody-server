<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_name',
        'total',
        'status'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->code = static::generateCode();
        });
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'transaction_menus')
            ->withPivot(['quantity', 'total', 'price']);
    }

    private static function generateCode()
    {
        $code = 'tx_';
        // generate random str
        $length = 10;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, $charactersLength - 1)];
        }
        // check
        if (Transaction::where('code', $code)->exists()) {
            return static::generateCode();
        }
        // return
        return $code;
    }
}
