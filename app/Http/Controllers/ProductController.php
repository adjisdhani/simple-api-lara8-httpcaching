<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = [
            ['id' => 1, 'name' => 'Product A', 'price' => 10000],
            ['id' => 2, 'name' => 'Product B', 'price' => 20000],
        ];

        $timestamp = now()->timestamp;
        $timeWindow = floor($timestamp / 1);

        $etag = hash('sha256', json_encode($products) . $timeWindow);

        $clientEtag = $request->headers->get('If-None-Match');
        if ($clientEtag && $clientEtag === $etag) {
            return response()->json(null, 304);
        }

        return response()->json($products, 200, [
            'Cache-Control' => 'max-age=1, public',
            'ETag' => $etag,
        ]);
    }
}