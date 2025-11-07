<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function serve(Request $request)
    {
        // For API requests, don't serve frontend
        if (str_starts_with($request->path(), 'api/')) {
            abort(404);
        }

        // For admin requests, don't serve frontend
        if (str_starts_with($request->path(), 'admin')) {
            abort(404);
        }

        // Return a simple response that will be handled by the ProxyToFrontend middleware
        return response('', 200);
    }
}
