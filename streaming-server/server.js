const NodeMediaServer = require('node-media-server');
const express = require('express');
const { spawn } = require('child_process');
const path = require('path');
const fs = require('fs');

// Create streams directory if it doesn't exist
const streamsDir = path.join(__dirname, 'streams');
if (!fs.existsSync(streamsDir)) {
  fs.mkdirSync(streamsDir, { recursive: true });
}

const config = {
  rtmp: {
    port: 1935,
    chunk_size: 60000,
    gop_cache: true,
    ping: 30,
    ping_timeout: 60
  },
  http: {
    port: 8001, // Changed from 8000 to 8001 to avoid conflict with Laravel
    allow_origin: '*',
    mediaroot: streamsDir
  }
};

const nms = new NodeMediaServer(config);

// Store active FFmpeg processes
const ffmpegProcesses = new Map();

// Path to FFmpeg executable
const ffmpegPath = path.join(__dirname, 'ffmpeg', 'extracted', 'ffmpeg-2025-11-10-git-133a0bcb13-essentials_build', 'bin', 'ffmpeg.exe');

nms.on('preConnect', (id, args) => {
  console.log('[NodeEvent on preConnect]', `id=${id} args=${JSON.stringify(args)}`);
});

nms.on('postConnect', (id, args) => {
  console.log('[NodeEvent on postConnect]', `id=${id} args=${JSON.stringify(args)}`);
});

nms.on('doneConnect', (id, args) => {
  console.log('[NodeEvent on doneConnect]', `id=${id} args=${JSON.stringify(args)}`);
});

nms.on('prePublish', (id, StreamPath, args) => {
  console.log('[NodeEvent on prePublish]', `id=${id} StreamPath=${StreamPath} args=${JSON.stringify(args)}`);
});

nms.on('postPublish', (id, StreamPath, args) => {
  console.log('[NodeEvent on postPublish]', `id=${id} StreamPath=${StreamPath} args=${JSON.stringify(args)}`);
});

nms.on('donePublish', (id, StreamPath, args) => {
  console.log('[NodeEvent on donePublish]', `id=${id} StreamPath=${StreamPath} args=${JSON.stringify(args)}`);
});

nms.run();

// Add Express server for additional API endpoints
const app = express();
app.use(express.json());
const PORT = 3001;

// API endpoint to start a stream
app.post('/api/start-stream/:cctvId', async (req, res) => {
  const { cctvId } = req.params;
  const { rtsp_url } = req.body; // Get RTSP URL from request body
  
  try {
    // Check if stream is already running
    if (ffmpegProcesses.has(cctvId)) {
      return res.json({ 
        message: 'Stream already running', 
        streamUrl: `http://127.0.0.1:8001/live/${cctvId}/index.m3u8` // Updated port
      });
    }
    
    // Use the provided RTSP URL or fetch from Laravel backend
    let rtspUrl = rtsp_url;
    
    // If no RTSP URL provided in request, fetch from Laravel backend
    if (!rtspUrl) {
      try {
        // Fetch CCTV details from Laravel backend
        const cctvResponse = await fetch(`http://127.0.0.1:8000/api/cctvs/${cctvId}`);
        if (cctvResponse.ok) {
          const cctvData = await cctvResponse.json();
          if (cctvData.data && cctvData.data.ip_rtsp_url) {
            rtspUrl = cctvData.data.ip_rtsp_url;
          }
        }
      } catch (error) {
        console.error('Error fetching CCTV data from Laravel:', error);
      }
    }
    
    // If still no RTSP URL, use fallback
    if (!rtspUrl) {
      rtspUrl = 'rtsp://wowzaec2demo.streamlock.net/vod/mp4:BigBuckBunny_115k.mov';
    }
    
    // Log the RTSP URL being used
    console.log('Using RTSP URL:', rtspUrl);
    
    // Start FFmpeg process to convert RTSP to HLS
    const hlsOutput = path.join(streamsDir, cctvId);
    if (!fs.existsSync(hlsOutput)) {
      fs.mkdirSync(hlsOutput, { recursive: true });
    }
    
    const ffmpegArgs = [
      '-i', rtspUrl,
      '-c:v', 'libx264',
      '-preset', 'ultrafast',
      '-tune', 'zerolatency',
      '-g', '60', // Force keyframe every 60 frames (2s at 30fps)
      '-sc_threshold', '0', // Disable scene change detection for strict GOP
      '-c:a', 'aac',
      '-ar', '44100',
      '-f', 'hls',
      '-hls_time', '2',
      '-hls_list_size', '5',
      '-hls_flags', 'delete_segments',
      '-hls_segment_filename', path.join(hlsOutput, 'segment%03d.ts'),
      path.join(hlsOutput, 'index.m3u8')
    ];
    
    console.log('Starting FFmpeg with args:', ffmpegArgs.join(' '));
    
    const ffmpegProcess = spawn(ffmpegPath, ffmpegArgs);
    
    ffmpegProcess.stdout.on('data', (data) => {
      console.log(`FFmpeg stdout: ${data}`);
    });
    
    ffmpegProcess.stderr.on('data', (data) => {
      console.log(`FFmpeg stderr: ${data}`);
    });
    
    ffmpegProcess.on('close', (code) => {
      console.log(`FFmpeg process for CCTV ${cctvId} exited with code ${code}`);
      ffmpegProcesses.delete(cctvId);
    });
    
    ffmpegProcesses.set(cctvId, ffmpegProcess);
    
    // Wait a moment for the stream to start
    setTimeout(() => {
      res.json({ 
        message: 'Stream started', 
        streamUrl: `http://127.0.0.1:8001/live/${cctvId}/index.m3u8` // Updated port
      });
    }, 2000);
    
  } catch (error) {
    console.error('Error starting stream:', error);
    res.status(500).json({ error: 'Failed to start stream' });
  }
});

// API endpoint to stop a stream
app.post('/api/stop-stream/:cctvId', (req, res) => {
  const { cctvId } = req.params;
  
  if (ffmpegProcesses.has(cctvId)) {
    const ffmpegProcess = ffmpegProcesses.get(cctvId);
    ffmpegProcess.kill('SIGINT');
    ffmpegProcesses.delete(cctvId);
    res.json({ message: 'Stream stopped' });
  } else {
    res.status(404).json({ error: 'Stream not found' });
  }
});

// API endpoint to check if a stream is running
app.get('/api/stream-status/:cctvId', (req, res) => {
  const { cctvId } = req.params;
  const isRunning = ffmpegProcesses.has(cctvId);
  res.json({ 
    cctvId, 
    isRunning,
    streamUrl: isRunning ? `http://127.0.0.1:8001/live/${cctvId}/index.m3u8` : null // Updated port
  });
});

app.listen(PORT, () => {
  console.log(`Streaming API server listening on port ${PORT}`);
});

console.log('Node Media Server started');
console.log('RTMP server listening on port 1935');
console.log(`HTTP server listening on port ${config.http.port}`);
console.log(`Streaming API server listening on port ${PORT}`);