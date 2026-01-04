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
            // Start the actual stream conversion by calling the streaming server
            $streamUrl = 'http://127.0.0.1:8001/live/' . $id . '/index.m3u8';

            try {
                // Make an API call to the streaming server to start the stream with the actual RTSP URL
                $response = Http::timeout(10)->post('http://127.0.0.1:3001/api/start-stream/' . $id, [
                    'rtsp_url' => $cctv->ip_rtsp_url
                ]);

                if ($response->successful()) {
                    $streamData = $response->json();
                    $streamUrl = $streamData['streamUrl'];
                }
            } catch (\Exception $e) {
                Log::error('Failed to start stream', [
                    'cctv_id' => $id,
                    'rtsp_url' => $cctv->ip_rtsp_url,
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
