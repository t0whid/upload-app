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
        'batch_id',
        'file_order',
        'metadata',
        'expires_at',
    ];

    
    protected $casts = [
        'metadata' => 'array',
        'expires_at' => 'datetime',
    ];
}