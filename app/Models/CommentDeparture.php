<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentDeparture extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "comment_departures";
    protected $fillable = [
        'content',
        'customer_id',
        'departure_id',
        'star',
        'status',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function departure()
    {
        return $this->belongsTo(Departure::class, 'departure_id');
    }
}
