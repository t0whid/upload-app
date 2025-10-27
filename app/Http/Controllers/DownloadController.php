<?php

namespace App\Http\Controllers;

use App\Models\UploadedFile;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function show($slug)
    {
        $file = UploadedFile::where('slug', $slug)->firstOrFail();
        return view('download', ['file' => $file]);
    }
}
