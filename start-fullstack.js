const { spawn } = require('child_process');
const path = require('path');

console.log('🚀 Starting Pertamina Fullstack Application...');
console.log('==========================================\n');

// Function to start a process with optimized settings
function startProcess(command, args, cwd, name) {
  console.log(`🔄 Starting ${name}...`);
  const process = spawn(command, args, { 
    cwd, 
    stdio: 'pipe', 
    shell: true
  });
  
  process.stdout.on('data', (data) => {
    console.log(`[${name}] ${data.toString().trim()}`);
  });
  
  process.stderr.on('data', (data) => {
    console.log(`[${name} ERROR] ${data.toString().trim()}`);
  });
  
  process.on('close', (code) => {
    console.log(`[${name}] Process exited with code ${code}`);
  });
  
  process.on('error', (err) => {
    console.error(`[ERROR] Failed to start ${name}:`, err.message);
  });
  
  return process;
}

// Start Laravel backend (PHP server) on port 8000
const laravelProcess = startProcess(
  'php', 
  ['artisan', 'serve', '--port=8000'], 
  path.join(__dirname, 'backend-new'), 
  'Laravel Backend'
);

// Start Next.js frontend (Next.js dev server) on port 3000
const nextProcess = startProcess(
  'npm', 
  ['run', 'dev'], 
  path.join(__dirname, 'pertamina-frontend-build'), 
  'Next.js Frontend'
);

// Start Streaming server on port 8000 (RTMP on 1935, HTTP on 8000, API on 3000)
const streamingProcess = startProcess(
  'node', 
  ['server.js'], 
  path.join(__dirname, 'streaming-server'), 
  'Streaming Server'
);

// Graceful shutdown
process.on('SIGINT', () => {
  console.log('\n🛑 Shutting down servers...');
  if (laravelProcess && !laravelProcess.killed) laravelProcess.kill();
  if (nextProcess && !nextProcess.killed) nextProcess.kill();
  if (streamingProcess && !streamingProcess.killed) streamingProcess.kill();
  process.exit(0);
});

process.on('SIGTERM', () => {
  console.log('\n🛑 Shutting down servers...');
  if (laravelProcess && !laravelProcess.killed) laravelProcess.kill();
  if (nextProcess && !nextProcess.killed) nextProcess.kill();
  if (streamingProcess && !streamingProcess.killed) streamingProcess.kill();
  process.exit(0);
});

// Check if processes started successfully
setTimeout(() => {
  console.log('\n✅ Fullstack application startup initiated!');
  console.log('🌐 Access your application at: http://127.0.0.1:8000');
  console.log('📡 Streaming server RTMP available on port 1935');
  console.log('📡 Streaming server HTTP available on port 8001');
  console.log('📡 Streaming server API available on port 3000');
  console.log('🔄 Hot reload is enabled for development');
  console.log('⚠️  Press Ctrl+C to stop all servers\n');
}, 3000);