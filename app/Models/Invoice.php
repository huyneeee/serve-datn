<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'invoices';
    protected $fillable = [
        'departure_id',
        'customers_id',
        'phone',
        'note',
        'name',
        'email',
        'go_point',
        'come_point',
        'quantity',
        'total_price',
        'date',
        'invoice_code',
        'status',
        'form_payment'
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customers_id');
    }
    public function departure()
    {
        return $this->belongsTo(Departure::class, 'departure_id');
    }
    public function payment_invoice()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }
}
