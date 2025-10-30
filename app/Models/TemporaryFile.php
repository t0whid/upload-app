<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'download_url',
        'original_url',
        'expires_at',
        'batch_id'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];
}