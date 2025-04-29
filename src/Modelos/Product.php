<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category_id',
        'image',
        'status',
        'rating'
    ];

    // Add validation for rating to ensure it is between 1 and 5 with increments of 0.5
    public function setRatingAttribute($value)
    {
        if ($value >= 1 && $value <= 5 && fmod($value, 0.5) === 0.0) {
            $this->attributes['rating'] = $value;
        } else {
            throw new \InvalidArgumentException('Rating must be between 1 and 5, in increments of 0.5.');
        }
    }
}
