<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $table = "tasks";
    protected $fillable = [
        'description','is_completed','card_id'
    ];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}
