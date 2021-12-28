<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'cars';
    protected $fillable = [
        'name',
        'image',
        'description',
        'license_plates',
        'number_seats',
        'status',
        'color',
        'car_phone',
    ];
    public function car_images()
    {
        return $this->hasMany(CarImage::class, 'car_id');
    }
    public function policies()
    {
        return $this->belongsToMany(Policy::class, 'car_policy', 'car_id', 'policy_id')->withTimestamps();
    }
}
