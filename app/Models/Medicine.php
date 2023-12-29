<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;
    protected $table = 'medicines';

    protected $fillable = [
        'business_name',
        'medical_name',
        'manifacture',
        'description',
        'price',
        'category_id',
        'expire',
        'quantity',
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }
}
