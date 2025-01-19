<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FooterController extends Controller
{
    public function getFooterData(Request $request)
    {
        $footerData = DB::table('footer')->first();

        return response()->json([
            'status' => 'success',
            'data' => $footerData,
        ]);
    }
}
