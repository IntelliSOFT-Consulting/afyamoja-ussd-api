<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PDFController extends Controller
{
    public function show()
    {
        $file = base_path(). "/public/pdf/user_guide.pdf";
        $name = "User Guide.pdf";
        $headers = ['Content-Type: application/pdf'];

        return response()->download($file, $name, $headers);
    }
}
