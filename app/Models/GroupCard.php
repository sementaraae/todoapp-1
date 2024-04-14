<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupCard extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "activities";
    protected $fillable = [
        'name','color','user_id'
    ];
    public $timestamps = false;
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }
}
