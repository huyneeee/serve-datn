<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentNews extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "comment_news";
    protected $fillable = [
        'content',
        'customer_id',
        'news_id',
        'status',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function new()
    {
        return $this->belongsTo(News::class, 'news_id');
    }
}
