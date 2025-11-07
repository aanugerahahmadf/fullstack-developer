<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    public function testProxy()
    {
        try {
            $url = 'http://127.0.0.1:3001';
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => 'User-Agent: Laravel Proxy'
                ]
            ]);

            $content = file_get_contents($url, false, $context);
            return response($content, 200)->header('Content-Type', 'text/html');
        } catch (\Exception $e) {
            return response('Error: ' . $e->getMessage(), 500);
        }
    }
}
