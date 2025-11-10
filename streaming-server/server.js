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
    port: 8002,
    allow_origin: '*',
    mediaroot: streamsDir
  }
};

const nms = new NodeMediaServer(config);

// Store active FFmpeg processes
const ffmpegProcesses = new Map();

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
const PORT = 3002;

// API endpoint to start a stream
app.get('/api/start-stream/:cctvId', async (req, res) => {
  const { cctvId } = req.params;
  
  try {
    // In a real implementation, you would fetch the RTSP URL from your Laravel backend
    // For now, we'll simulate this with a test URL
    const response = await fetch(`http://127.0.0.1:8000/api/cctvs/${cctvId}`);
    const cctvData = await response.json();
    const rtspUrl = cctvData.data.ip_rtsp_url;
    
    // For testing purposes, we'll use a sample RTSP URL if the above fails
    // In production, replace this with the actual RTSP URL from the database
    // const rtspUrl = 'rtsp://wowzaec2demo.streamlock.net/vod/mp4:BigBuckBunny_115k.mov';
    
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
    
    const ffmpegProcess = spawn('ffmpeg', ffmpegArgs);
    
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
        streamUrl: `http://127.0.0.1:8000/live/${cctvId}/index.m3u8` 
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
    streamUrl: isRunning ? `http://127.0.0.1:8000/live/${cctvId}/index.m3u8` : null
  });
});

app.listen(PORT, () => {
  console.log(`Streaming API server listening on port ${PORT}`);
});

console.log('Node Media Server started');
console.log('RTMP server listening on port 1935');
console.log('HTTP server listening on port 8000');
console.log(`Streaming API server listening on port ${PORT}`);