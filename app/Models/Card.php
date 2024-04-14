<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;
    protected $table = "cards";
    protected $fillable = [
        'name','color','activity_id'
    ];

    public function activity()
    {
        return $this->belongsTo(GroupCard::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
