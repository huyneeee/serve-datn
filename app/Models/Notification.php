<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notifications';
    protected $fillable = [
        'token_device',
        'invoice_id',
        'title',
        'content',
        'user_id',
        'status',
        'is_send',
        'role_id',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }
    public function notification_role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
