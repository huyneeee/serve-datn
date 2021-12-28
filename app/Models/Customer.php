<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable implements MustVerifyEmail
{

    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    protected $table = 'customers';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $fillable = [
        'last_name',
        'first_name',
        'email',
        'password',
        'address',
        'phone_number',
        'image',
        'status',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address' => 'array',
    ];
    public function sendPasswordResetNotification($token)
    {
        $url = 'http://127.0.0.1:8000/reset-password?token=' . $token;
        $this->notify(new ResetPasswordNotification($url));
    }
    public function comment_departure()
    {
        return $this->hasMany(CommentDeparture::class, 'customer_id');
    }
    public function customer_invoice()
    {
        return $this->hasMany(Invoice::class, 'customers_id');
    }
}
