<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    /**
     * Get system statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Ultra-fast cached statistics with 0.5 second TTL
            $stats = Cache::remember('system_stats', 0.5, function () {
                return [
                    'total_buildings' => DB::table('buildings')->count(),
                    'total_rooms' => DB::table('rooms')->count(),
                    'total_cctvs' => DB::table('cctvs')->count(),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $stats
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}
