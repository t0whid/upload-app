<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadedFile extends Model
{
    use HasFactory;
    protected $fillable = [
        'slug', 'filename', 'download_url', 'expires_at', 'remove_url', 'size', 'whirlpool'
    ];
}
