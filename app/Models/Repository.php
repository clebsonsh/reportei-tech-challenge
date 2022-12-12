<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repository extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'commits',
        'user_id',
    ];

    protected $casts = [
        'commits' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
