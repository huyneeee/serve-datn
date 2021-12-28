<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewCategory extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'new_categories';
    protected $fillable = [
        'name',
        'image',
        'parent_id',
        'short_content',
        'content',
        'slug',
    ];
    public function children()
    {
        return $this->hasMany(NewCategory::class, 'parent_id')->with('children');
    }
}
