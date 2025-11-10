<?php

namespace App\Repositories;

use App\Models\Cctv;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CctvRepository extends BaseRepository
{
    public function __construct(Cctv $model)
    {
        parent::__construct($model);
    }

    public function getByRoomId(int $roomId)
    {
        return $this->model
            ->select('id', 'room_id', 'name', 'ip_rtsp_url')
            ->where('room_id', $roomId)
            ->orderBy('id')
            ->get();
    }

    public function getStreamUrl(int $id)
    {
        $cctv = $this->find($id);
        if ($cctv) {
            // In a production environment, you would start the actual stream conversion here
            // For now, we'll return a URL that points to our streaming server
            $streamUrl = 'http://127.0.0.1:8000/live/' . $id . '/index.m3u8';

            // In a real implementation, you would make an API call to your streaming server:
            try {
                // Uncomment this when you have FFmpeg properly installed and configured
                /*
                $response = Http::timeout(10)->get('http://127.0.0.1:3002/api/start-stream/' . $id);

                if ($response->successful()) {
                    $streamData = $response->json();
                    $streamUrl = $streamData['streamUrl'];
                }
                */
            } catch (\Exception $e) {
                Log::error('Failed to start stream', [
                    'cctv_id' => $id,
                    'error' => $e->getMessage()
                ]);
                // Fall back to direct URL
                $streamUrl = $cctv->ip_rtsp_url;
            }

            // Log the RTSP URL for debugging
            Log::info('CCTV Stream Request', [
                'cctv_id' => $id,
                'rtsp_url' => $cctv->ip_rtsp_url
            ]);

            return [
                'stream_url' => $streamUrl,
                'rtsp_url' => $cctv->ip_rtsp_url
            ];
        }
        return null;
    }
}
