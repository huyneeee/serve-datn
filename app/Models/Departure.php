<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departure extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'departures';
    protected $fillable = [
        'name',
        'user_id',
        'car_id',
        'price',
        'go_location_city',
        'go_location_district',
        'go_location_wards',
        'come_location_city',
        'come_location_district',
        'come_location_wards',
        'start_time',
        'end_time',
        'seats_departures',
        'departure_code',
    ];

    public function car_departure()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function invoice_departure()
    {
        return $this->hasMany(Invoice::class, 'departure_id');
    }
    public function comment_departure()
    {
        return $this->hasMany(CommentDeparture::class, 'departure_id');
    }
    public function user_departure()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
