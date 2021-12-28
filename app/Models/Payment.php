<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payments';
    protected $fillable = [
        'invoice_id',
        'price',
        'note',
        'date',
        'vnp_response_code',
        'code_vnpay',
        'code_bank',
    ];
    public function payment_invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
