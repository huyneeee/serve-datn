<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarImage extends Model
{
    use HasFactory;
    protected $table = 'car_images';
    protected $fillable = [
        'image_path',
        'car_id',
    ];
}
