<?php

namespace App\Http\Controllers;

use App\Models\UploadedFile;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function show($slugs)
    {
        $slugArray = explode(',', $slugs);
        $files = UploadedFile::whereIn('slug', $slugArray)->get();
        return view('download', ['files' => $files]);
    }
}
